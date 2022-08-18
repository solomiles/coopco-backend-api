<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Superadmin;
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
            'username' => 'required',
            'password' => 'required'
        ]);

        if($validator->fails()) {
            return response([
                'status' => false,
                'errors' => $validator->errors()->messages()
            ], 400);
        }

        $invalidCredentialsResponse = [
            'status' => false,
            'message' => 'Invalid Credentials'
        ];

        $username = $request->username;
        $password = $request->password;

        switchSchema($request, 'main');
        $superadmin = Superadmin::where('username', $username)->first();

        if (!$superadmin) {
            return response()->json($invalidCredentialsResponse, 401);
        }

        if (!Hash::check($password, $superadmin->password)) {
            return response()->json($invalidCredentialsResponse, 401);
        }

        $token = $superadmin->createToken('Coopco Superadmin Token', ['superadmins']);
       
        $data = [
            'superadmin' => $superadmin,
            'token' => $token->accessToken,
            'token_type' => 'Bearer',
            'token_expires' => Carbon::parse(
                $token->token->expires_at
            )->toDateTimeString(),
        ];

        return response([
            'status' => true,
            'message' => 'Login Successful',
            'data' => $data
        ], 200);
    }
}

?>