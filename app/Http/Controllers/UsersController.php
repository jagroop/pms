<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;

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
        return view('users.index')->with('users', $users);
    }

    /**
     * Display a listing of the resource searched.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $users = $this->service->search($request->search);
        return view('users.index')->with('users', $users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\UserCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserCreateRequest $request)
    {
        $result = $this->service->create($request->except('_token'));

        if ($result) {
            return redirect(route('users.edit', ['id' => $result->id]))->with('message', 'Successfully created');
        }

        return redirect(route('users.index'))->withErrors('Failed to create');
    }

    /**
     * Display the user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->service->find($id);
        return view('users.show')->with('user', $user);
    }

    /**
     * Show the form for editing the user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = $this->service->find($id);
        return view('users.edit')->with('user', $user);
    }

    /**
     * Update the users in storage.
     *
     * @param  App\Http\Requests\UserUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, $id)
    {
        $result = $this->service->update($id, $request->except('_token'));

        if ($result) {
            return back()->with('message', 'Successfully updated');
        }

        return back()->withErrors('Failed to update');
    }

    /**
     * Remove the users from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = $this->service->destroy($id);

        if ($result) {
            return redirect(route('users.index'))->with('message', 'Successfully deleted');
        }

        return redirect(route('users.index'))->withErrors('Failed to delete');
    }
}
