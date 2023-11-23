<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function __invoke(Request $request)
    {
        sleep(3);
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()]
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        event(new Registered($user));

        // $device = substr($request->userAgent() ?? '', 0, 255);

        // return response()->json([
        //     'access_token' => $user->createToken($device)->plainTextToken
        // ], Response::HTTP_CREATED);
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully'
        ]);
    }
}
