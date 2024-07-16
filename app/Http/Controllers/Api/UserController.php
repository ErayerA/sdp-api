<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ShowUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return UserResource::collection(User::with(User::getAllIncludes())->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): UserResource
    {
        $user = User::create($request->validated());
        return new UserResource($user->loadMissing($user->allIncludes));
    }

    /**
     * Display the specified resource.
     */
    public function show(ShowUserRequest $request, User $user): UserResource
    {
        return new UserResource($user->loadMissing($user->allIncludes));
    }
}
