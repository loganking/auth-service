<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Token;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
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

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $this->validate($request, [
            'name' => 'required_without:password|string|max:100',
            'password' => 'required_without:name|string|max:100',
        ]);

        if (!empty($request->name)) {
            $user->name = $request->name;
        }

        if (!empty($request->password)) {
            $user->password = app('hash')->make($request->password);
        }

        $user->save();

        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json($user);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)
            ->firstOrFail();

        if (!app('hash')->check($request->password, $user->password)) {
            return response()->json([], 401);
        }

        $token = Token::create([
            'user_id' => $user->id,
            'token' => Str::random(),
            'expires_at' => Carbon::now()->add('1 hour'),
        ]);
        return response()->json($token);
    }

    public function logout(Request $request)
    {
        Token::where('user_id', $request->user()->id)->delete();
        return response()->json([], 204);
    }
}
