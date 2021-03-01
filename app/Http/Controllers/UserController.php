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
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users|max:100',
            'password' => 'required|max:100',
        ]);

        $hash = app('hash')->make($request->password);
        $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $hash,
        ]);

        return response()->json($user, 201);
    }

    public function update(Request $request, $userId)
    {
        $this->validate($request, [
            'name' => 'string|max:100',
            'password' => 'string|max:100',
        ]);

        $user = User::findOrFail($userId);
        if (!empty($request->name)) {
            $user->name = $request->name;
        }

        if (!empty($request->password)) {
            $user->password = app('hash')->make($request->password);
        }

        $user->save();

        return response()->json($user);
    }

    public function destroy()
    {
        $user = User::findOrFail($userId);
        $user->delete();

        return response()->json($user);
    }

    public function login()
    {
        //
    }

    public function logout()
    {
        //
    }
}
