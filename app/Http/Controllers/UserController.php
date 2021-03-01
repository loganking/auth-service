<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        $hash = app('hash')->make($request->password);
        $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $hash,
        ]);

        return response()->json($user, 201);
    }

    public function list()
    {
        $users = app('db')->table('user')->get();

        return response()->json($users);
    }

    //
}
