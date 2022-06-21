<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Cooperative;
use App\Models\EmailCredentials;
use App\Models\Member;
use App\Traits\EmailTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CRUDController extends Controller
{
    use EmailTrait;

    /**
     * Create new member
     * @param Request $request
     * 
     * @return Response
     */
    public function create(Request $request) {

        $validate = $this->validator($request);
        if($validate->fails()) {
            return response([
                'status' => 400,
                'errors' => $validate->errors()->messages()
            ], 400);
        }

        $member = new Member();
        $this->store($request, $member);
        
        $member->password = randomPassword();

        $send = $this->sendWelcomeEmail($member);
        if(!$send) {
            return response([
                'status' => false,
                'message' => g('SERVER_ERROR')
            ], 500);
        }

        $member->password = Hash::make($member->password);
        $member->save();

        return response([
            'status' => true,
            'message' => 'Member Created'
        ], 201);
    }

    /**
     * Member data validator
     * @param Request $request
     * 
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator($request) {

        $genders = ['male', 'female', 'other'];

        return Validator::make($request->all(), [
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'othernames' => 'string|max:100',
            'email' => 'required|email:filter,rfc,dns|unique:members',
            'phone' => 'required|max:15',
            'gender' => [
                'required',
                Rule::in($genders)
            ]
        ]);
    }

    /**
     * Store member data
     * @param Request $request
     * @param Member $member
     * 
     * @return void
     */
    public function store($request, $member) {
        $member->firstname = ucfirst($request->firstname);
        $member->lastname = ucfirst($request->lastname);
        $member->othernames = ucfirst($request->othernames ?? '');
        $member->email = strtolower($request->email);
        $member->phone = $request->phone;
        $member->gender = $request->gender;

        $member->save();
    }

    /**
     * Send welcome email to new member
     * @param Member $member
     * 
     * @return bool
     */
    public function sendWelcomeEmail($member) {

        $emailCredentials = EmailCredentials::firstOrFail();
        setEmailCredentials($emailCredentials);

        $emailSubject = 'Welcome to Coopco';
        $emailTemplate = 'welcomeMember';

        $cooperative = Cooperative::firstOrFail()->name;

        $emailData = [
            'cooperative' => $cooperative,
            'password' => $member->password
        ];

        return $this->sendSingleEmail($emailSubject, $member->email, $emailData, $emailTemplate);
    }
}
