<?php 

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UUID;
use Illuminate\Http\Request;

class UsersController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $users = User::all();

        return success(trans('users.index'), compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|string|min:4',
        ]);

        $request->merge(['verified_at' => now()]);
        
        $user = User::create($request->input());

        UUID::generate($user, 'api_token');

        return success(trans('users.store'), compact('user'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        
        return success(trans('users.show'), compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
        ]);

        $user = User::findOrFail($id);
        $user->update($request->only('first_name', 'last_name'));

        return success(trans('users.update'), compact('user'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return success(trans('users.destroy'));
    }
}
