<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordUpdateController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()]
        ]);
        // dd($request->all());

        if(!Hash::check($request->current_password, auth()->user()->password))
        {
            return response()->json([
                'status' => false,
                'message' => 'Password lama salah'
            ], 401);
        }
        auth()->user()->update([
            'password' => bcrypt($request->input('password'))
        ]);

        return response()->json([
            'message' => 'Your password has been updated.',
        ], Response::HTTP_ACCEPTED);
    }
}
