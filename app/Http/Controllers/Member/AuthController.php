<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Process login action
     * @param Request $request - Request object
     *
     * @return Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'status' => 400,
                'errors' => $validator->errors()->messages(),
            ], 400);
        }

        $invalidCredentialsResponse = [
            'status' => 401,
            'message' => 'Invalid Credentials',
        ];

        $email = $request->email;
        $password = $request->password;

        $member = Member::where('email', $email)->first();

        if (!$member) {
            return response()->json($invalidCredentialsResponse, 401);
        }

        if (!Hash::check($password, $member->password)) {
            return response()->json($invalidCredentialsResponse, 401);
        }

        $token = $member->createToken('Cooperative Member Token');

        $data = [
            'member' => $member,
            'token' => $token->accessToken,
            'token_type' => 'Bearer',
            'token_expires' => Carbon::parse(
                $token->token->expires_at
            )->toDateTimeString(),
        ];

        return response([
            'status' => 200,
            'message' => 'Login Successful',
            'data' => $data,
        ], 200);
    }
}
