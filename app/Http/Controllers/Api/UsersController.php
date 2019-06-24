<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Mail\NewUserCreated;
use Illuminate\Support\Facades\Mail;

class UsersController extends Controller
{
    public function __construct(UserService $user_service)
    {
        $this->service = $user_service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = $this->service->paginated();
        return UserResource::collection($users);
    }

    /**
     * Display a listing of the resource searched.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $users = $this->service->search($request->search);
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\UserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserCreateRequest $request)
    {
        $data = $request->except('_token');
        $password = str_random(15);
        $data['password'] = bcrypt($password);
        $result = $this->service->create($data);

         if ($result) {  
            $result->password = $password;
            Mail::to($result->email)->cc('jagroop.singh@kindlebit.com')->send(new NewUserCreated($result));
            $user = new UserResource($this->service->find($result->id));
            return response()->json($user);
        }

        return response()->json(['error' => 'Unable to create user'], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->service->find($id);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\UserRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, $id)
    {
        $result = $this->service->update($id, $request->except('_token'));

        if ($result) {
            $user = new UserResource($this->service->find($id));
            return response()->json($user);
        }

        return response()->json(['error' => 'Unable to update user'], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = $this->service->destroy($id);

        if ($result) {
            return response()->json(['success' => 'User was deleted'], 200);
        }

        return response()->json(['error' => 'Unable to delete user'], 500);
    }
}
