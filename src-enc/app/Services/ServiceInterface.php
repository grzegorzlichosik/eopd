<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Validators\ValidatorInterface;
use App\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\Cache;

interface ServiceInterface
{
}
