<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Utils\APIResponse;
use App\Traits\APITraits;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use APITraits;
    public function register(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8'
        ]);


        if ($validate->fails()) {
            $responseError = $this->response('Something Wrong', $validate->errors());
            return APIResponse::ErrorResponse($responseError, 400);
        }

        $user = User::create(array_merge($validate->validate(), ['password' => bcrypt($request->password)]));

        $token = $user->createToken('auth_token')->plainTextToken;

        $responseSuccesful = $this->response(
            'Successfully Regsiter',
            ['acess_token' => $token, 'token_type' => "Bearer", 'email' => $request->email]
        );

        return APIResponse::SuccessResponse($responseSuccesful, 200);
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            $response = $this->response('Invalid Login Details', []);

            return APIResponse::ErrorResponse($response, 401);
        }

        $user = User::where('email', $request->email)->first();

        $token = $user->createToken('auth_token')->plainTextToken;

        $responseSuccesful = $this->response('Succesfully Login', [
            'access_token' => $token,
            'token_type' => "Bearer"
        ]);

        return APIResponse::SuccessResponse($responseSuccesful, 200);
    }

    public function me(Request $request)
    {
        return APIResponse::SuccessResponse($request->user(), 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return APIResponse::SuccessResponse(['msg' => "Succesfully Logout"]);
    }
}
