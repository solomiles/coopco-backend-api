<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\EmailCredentials;
use App\Models\Member;
use App\Traits\EmailTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use EmailTrait;

    /**
     * Process login action
     * @param Request $request - Request object
     *
     * @return Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'status' => false,
                'errors' => $validator->errors()->messages(),
            ], 400);
        }

        $invalidCredentialsResponse = [
            'status' => false,
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
            'status' => true,
            'message' => 'Login Successful',
            'data' => $data,
        ], 200);
    }

    /**
     * Send password reset email
     * @param Request $request - Request object
     *
     * @return Response
     */
    public function sendPasswordResetEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:members',
        ]);

        if ($validator->fails()) {
            return response([
                'status' => false,
                'errors' => $validator->errors()->messages(),
            ], 400);
        }

        // Generate token
        $token = bin2hex(random_bytes(5));

        // Update member data with new token
        $member = Member::where('email', $request->email)->first();
        $member->remember_token = $token;
        $member->save();

        // Send email to user
        $emailCredentials = EmailCredentials::firstOrFail();
        setEmailCredentials($emailCredentials);

        $this->sendSingleEmail('Password Reset', $member->email, ['token' => $token], 'password-reset');

        return response([
            'status' => true,
            'message' => 'Email Sent Successfuly',
        ], 200);
    }

    /**
     * Render password reset form
     * @param Request $request - Request object
     *
     * @return Response
     */
    public function resetPassword(Request $request, $token)
    {
        $validator = Validator::make(['token' => $token], [
            'token' => 'required|string|exists:members,remember_token',
        ]);

        if ($validator->fails()) {
            return view('password-reset.token-error', ['errors' => $validator->errors()]);
        }

        // Check if token is expired
        $member = Member::where('remember_token', $request->token)->first();

        // dd($member->updated_at, now()->addMinute(1));
        $updatedAt = Carbon::parse($member->created_at);

        if ($member->updated_at->diffInHours(now()) > 2) {
            return view('password-reset.token-error', ['errors' => 'The token is expired.']);
        }

        return view('password-reset.password-reset');
    }
}
