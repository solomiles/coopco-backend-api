<?php

namespace App\Http\Controllers\Member;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Illuminate\Support\Facades\Storage;

class CRUDController extends AccessTokenController
{
    /**
     * Store member data
     * @param Request $request
     * @param Member $member
     *
     * @return void
     */
    public function store($request, $member)
    {
        if($request->input('photo')) {
            // Store profile photo
            $photo = base64ToFile($request->input('photo'));
            $photoName = $photo->hashName();
            $photo->store('public/members/photo');
        }
        else {
            $photoName = $member->photo;
        }

        // Update member data
        $member->firstname = ucfirst($request->firstname);
        $member->lastname = ucfirst($request->lastname);
        $member->othernames = ucfirst($request->othernames ?? '');
        $member->phone = $request->phone;
        $member->gender = $request->gender;
        $member->photo = $photoName;

        if($request->password) $member->password = Hash::make($request->password);

        $member->save();
    }

    /**
     * Member data validator
     * @param Request $request
     * @param array $customRules
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator($request)
    {
        return Validator::make($request->all(), [
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'othernames' => 'string|max:100',
            'phone' => 'required|max:15',
            'photo' => 'base64image|base64mimes:png,jpg,jpeg|base64max:6000',
            'password' => 'confirmed',
        ]);
    }

    /**
     * Update member
     * @param Request $request
     * @param int $memberId Member Id
     * @return Response
     */
    public function update(Request $request, int $memberId)
    {
        // Validate form fields
        $validate = $this->validator($request);

        if ($validate->fails()) {
            return response([
                'status' => false,
                'errors' => $validate->errors()->messages(),
            ], 400);
        }

        $member = Member::findOrFail($memberId);
        $oldPhoto = $member->photo;
        
        $this->store($request, $member);

        if($oldPhoto != 'default-member.png' && $request->input('photo')) {
            Storage::delete('/public/members/photo/'.$oldPhoto);
        }

        return response([
            'status' => true,
            'message' => 'Member Updated',
        ], 200);
    }
}
