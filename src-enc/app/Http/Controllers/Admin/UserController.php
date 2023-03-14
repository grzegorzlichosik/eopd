<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organisation;
use App\Notifications\InviteNewUser;
use App\Notifications\NewUserInvite;
use App\Validators\DeleteUserValidator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Models\User;
use App\Validators\NewUserValidator;
use App\Validators\UserValidator;

class UserController extends Controller
{
    private const ROUTE = 'admin.users';

    public function __construct(
        private readonly UserValidator       $userValidator,
        private readonly NewUserValidator    $newUserValidator,
        private readonly DeleteUserValidator $deleteUserValidator
    )
    {
    }

    public function index(): \Inertia\Response
    {
        list($sortField, $sortOrder) = getTableSorting();

        $users = User::where('organisations_id', auth()->user()->organisations_id)
            ->with('pools:id,name')
            ->when(request()->input('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    return $q->orWhere('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->when(request()->input('email_verified_at'), function ($query, $search) {
                $method = $search === '1' ? 'whereNotNull' : 'whereNull';
                return $query->$method('email_verified_at');
            })
            ->when(request()->input('role'), function ($query, $search) {
                $key = Str::camel($search);
                return $query->$key();
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate(20)
            ->appends(cleanupAppends(request()->input()));

        collect($users->items())->map(function ($user) {
            $user->active_user = auth()->user()->id == $user->id;
            $user->setRelation('pools', $user->pools->pluck('name'));
            $user->upcoming_meetings = 0;
            return $user;
        });

        return Inertia::render('Admin/Users', [
            'users' => $users,
            'roles' => $this->getRoles(),
            'emailStatus' => [
                ['name' => 'Pending verification', 'value' => -1 ],
                ['name' => 'Verified', 'value' => 1 ]
            ],
            'searchFilters' => [
                'role'              => request()->input('role'),
                'email_verified_at' => request()->input('email_verified_at'),
                'searchValue'       => request()->input('search')
            ],

        ]);

    }

    private function getRoles(): array
    {
        $roles = [
            [
                'value' => 'is_super_admin',
                'name'  => trans('roles.super_administrator')
            ],
            [
                'value' => 'is_admin',
                'name'  => trans('roles.administrator')
            ],
            [
                'value' => 'is_agent',
                'name'  => trans('roles.agent')
            ],
            [
                'value' => 'is_developer',
                'name'  => trans('roles.developer')
            ]
        ];

        if (!auth()->user()->is_super_admin) {
            unset($roles[0]);
        }

        return array_values($roles);
    }

    public function create(Request $request): RedirectResponse
    {
        $request = $request->all();
        $this->newUserValidator->validate($request);

        $user = User::create(
            array_merge(
                [
                    'name'                => $request['name'],
                    'email'               => $request['email'],
                    'organisations_id'    => auth()->user()->organisations_id,
                    'password'            => Hash::make(Str::random(12)),
                    'password_updated_at' => now(),
                ],
                $this->getRolesFromArray($request['roles'])
            )
        );

        $this->sendUserInvite($user);

        return Redirect::route(self::ROUTE)
            ->with('toaster', [
                    'message' => trans('modals.user_created', ['user' => $user->name])
                ]
            );
    }

    public function update(string $uuid, Request $request): RedirectResponse
    {
        $request = $request->all();
        $this->userValidator->validate($request);

        $user = $this->getUser($uuid);
        $userEmail = $user->email;

        $roles = $this->getRolesFromArray($request['roles']);
        unset($request['roles']);

        $user->fill(
            array_merge(
                $request,
                $roles
            )
        );
        $user->save();

        $user = $user->refresh();
        $successMessage = trans('modals.user_updated', ['user' => $user->name]);

        /**
         * Send New Invite only when email was updated
         */
        if ($user->email !== $userEmail) {
            $this->sendUserInvite($user);
            $successMessage = trans('modals.user_updated_with_invite_email', ['user' => $user->name]);
        }

        return Redirect::route(self::ROUTE, [], 303)
            ->with('toaster', [
                    'message' => $successMessage]
            );

    }

    public function destroy(string $uuid): RedirectResponse
    {
        $this->deleteUserValidator->validate(['uuid' => $uuid]);

        $user = $this->getUser($uuid);
        $user->delete();

        $user->email = $user->email . '_' . Str::random();
        $user->save();

        return Redirect::route(self::ROUTE)
            ->with('toaster', [
                    'message' => trans('modals.user_deleted', ['user' => $user->name])
                ]
            );
    }

    private function getRolesFromArray(array $data): array
    {
        $result = [
            'is_agent'     => in_array('is_agent', $data),
            'is_developer' => in_array('is_developer', $data),
            'is_admin'     => in_array('is_admin', $data),
        ];

        if (auth()->user()->is_super_admin) {
            $result['is_super_admin'] = in_array('is_super_admin', $data);
        }

        return $result;
    }

    private function sendUserInvite(User $user): void
    {
        $organisation = Organisation::where('id', Auth::user()->organisations_id)->firstorFail();

        $token = Password::broker(config('fortify.passwords'))->createToken($user);

        $user->notify(new InviteNewUser($token, Auth::user()->name, $organisation->name));
    }

    private function getUser(string $uuid): ?User
    {
        return User::where('organisations_id', auth()->user()->organisations_id)
            ->where('uuid', $uuid)
            ->firstOrFail();
    }
}
