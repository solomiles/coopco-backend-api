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

    public function create(Request $request) {

        $validator = $this->createValidator($request);
        if($validator->fails()) {
            return response([
                'status' => 400,
                'errors' => $validator->errors()->messages()
            ], 400);
        }

        $member = new Member();
        $this->store($request, $member);
        
        $member->password = randomPassword();

        $send = $this->sendWelcomeEmail($member);
        if(!$send) {
            return response([
                'status' => false,
                'message' => 'Something has gone wrong, please try again'
            ], 500);
        }

        $member->password = Hash::make($member->password);
        $member->save();

        return response([
            'status' => true,
            'message' => 'Member Created'
        ], 201);
    }

    public function createValidator($request) {

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

    public function store($request, $member) {
        $member->firstname = ucfirst($request->firstname);
        $member->lastname = ucfirst($request->lastname);
        $member->othernames = ucfirst($request->othernames ?? '');
        $member->email = strtolower($request->email);
        $member->phone = $request->phone;
        $member->gender = $request->gender;

        $member->save();
    }

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
