<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Hash;
use Session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use function PHPUnit\Framework\name;

class CustomAuthController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function exercise()
    {
        return view('exercise');
    }

    public function getLogin(Request $request)
    {
        return view("login");
    }

    public function postVoiceAnalyst(Request $request)
    {
        $id = Auth::id();
        $prefix = str_pad($id, 6, "0", STR_PAD_LEFT);
        $date = date("YmdHis");

        if(@$_FILES['file1']) {
            Storage::put("{$prefix}-exercise-1-{$date}.mp3", file_get_contents($_FILES['file1']['tmp_name']));
        }
        if(@$_FILES['file2']) {
            Storage::put("{$prefix}-exercise-2-{$date}.mp3", file_get_contents($_FILES['file2']['tmp_name']));
        }
        if(@$_FILES['file3']) {
            Storage::put("{$prefix}-exercise-3-{$date}.mp3", file_get_contents($_FILES['file3']['tmp_name']));
        }

        return redirect("exercise");
    }

    public function postLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->intended('/exercise');
//                ->withSuccess('You have signed in as ' . Auth::user()->email);
        }

        return redirect("login")->withError('Login details are not valid');
    }

    public function postRegistration(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        $data = $request->all();
        $check = $this->create($data);

        $id = $check->id;
        $prefix = str_pad($id, 6, "0", STR_PAD_LEFT);
        $date = date("YmdHis");

        $filepath = public_path('/uploads/');
        $handle = fopen($filepath.$prefix."-user-".$date.".csv", 'w');
        fputcsv($handle, ["age","sex","since","medication","frequency","voice"], ',');
        fputcsv($handle, [@$data["age"],@$data["sex"],@$data["since"],@$data["medication"],@$data["frequency"],@$data["voice"]], ',');
        fclose($handle);

        Storage::put("{$prefix}-user-{$date}.csv", file_get_contents($filepath.$prefix."-user-".$date.".csv"));

        unlink($filepath.$prefix."-user-".$date.".csv");

        return $this->postLogin($request);
    }

    private function create(array $data)
    {
        return User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);
    }

    public function dashboard()
    {
        if(Auth::check()){
            return view('dashboard');
        }

        return redirect("login")->withSuccess('You are not allowed to access');
    }

    public function signOut() {
        Session::flush();
        Auth::logout();

        return Redirect('login');
    }
}
