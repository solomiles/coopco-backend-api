<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\EmailCredentials;
use App\Models\Member;
use App\Models\PasswordResetToken;
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
            'email' => 'required|email:filter,dns',
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
        $token = bin2hex(random_bytes(3)) . time();

        // Update member data with new token
        $resetToken = new PasswordResetToken();
        $resetToken->token = $token;
        $resetToken->email = $request->email;
        $resetToken->save();

        $sendEmail = $this->sendSingleEmail('Password Reset', $request->email, ['token' => $token], 'password-reset');
        if (!$sendEmail) {
            return response([
                'status' => false,
                'message' => g('SERVER_ERROR')
            ], 500);
        }

        return response([
            'status' => true,
            'message' => 'Email Sent Successfuly',
        ], 200);
    }

    /**
     * Render password reset form
     * @param string $token - The password reset token
     *
     * @return View
     */
    public function passwordResetForm($token)
    {

        $validator = Validator::make(['token' => $token], [
            'token' => 'required|string|exists:password_reset_tokens',
        ]);

        if ($validator->fails()) {
            return view('password-reset.token-error', ['errors' => $validator->errors()]);
        }

        // Check if token is expired
        $resetToken = PasswordResetToken::firstWhere('token', $token);

        if ($resetToken->created_at->diffInMinutes(now()) > 30) {
            return view('password-reset.token-error', ['errors' => 'The token is expired.']);
        }

        return view('password-reset.password-reset');
    }

    /**
     * Reset password
     * @param Request $request - Request object
     *
     * @return View
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|exists:password_reset_tokens',
            'password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return view('password-reset.password-reset', ['errors' => $validator->errors()]);
        }

        // Check if token is expired
        $resetToken = PasswordResetToken::firstWhere('token', $request->token);
        if ($resetToken->created_at->diffInMinutes(now()) > 80) {
            $resetToken->delete();
            return view('password-reset.token-error', ['errors' => 'The token is expired.']);
        }

        // Update password
        $member = Member::where('email', $resetToken->email)->first();
        $member->password = Hash::make($request->password);
        $member->save();

        // Delete current token
        $resetToken->delete();

        return redirect()->route('member.password-success');
    }
}
