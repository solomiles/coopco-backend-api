<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CRUDController extends Controller
{
    public function create(Request $request) {

        $validator = $this->createValidator($request);
        if($validator->fails()) {
            return response([
                'status' => 400,
                'errors' => $validator->errors()->messages()
            ], 400);
        }

        $member = new Member();

        $member->firstname = ucfirst($request->firstname);
        $member->lastname = ucfirst($request->lastname);
        $member->othernames = ucfirst($request->othernames ?? '');
        $member->email = strtolower($request->email);
        $member->phone = $request->phone;
        $member->gender = $request->gender;

        $member->save();
    }

    public function createValidator($request) {

        $genders = ['male', 'female', 'other'];

        return Validator::make($request->all(), [
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'othernames' => 'string|max:100',
            'email' => 'required|email:filter,rfc,dns,spoof|unique:members',
            'phone' => 'required|max:15',
            'gender' => [
                'required',
                Rule::in($genders)
            ]
        ]);
    }
}
