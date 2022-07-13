<?php

namespace App\Http\Controllers\Member;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Passport\Http\Controllers\AccessTokenController;

class CrudController extends AccessTokenController
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
        // Store profile photo
        $photo = base64ToFile($request->input('photo'));
        $photoName = $photo->hashName();
        $photoPath = $photo->store('public/profile-photo');

        // Update member data
        $member->firstname = ucfirst($request->firstname);
        $member->lastname = ucfirst($request->lastname);
        $member->othernames = ucfirst($request->othernames ?? '');
        $member->phone = $request->phone;
        $member->photo = $photoName;

        $member->save();
    }

    /**
     * Member data validator
     * @param Request $request
     * @param array $customRules
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator($request, $customRules = [])
    {

        $genders = ['male', 'female', 'other'];

        return Validator::make($request->all(), [
            'firstname' => $customRules['firstname'] ?? 'required|string|max:50',
            'lastname' => $customRules['lastname'] ?? 'required|string|max:50',
            'othernames' => $customRules['othernames'] ?? 'string|max:100',
            'phone' => $customRules['phone'] ?? 'required|max:15',
            'photo' => $customRules['photo'] ?? 'required|base64image|base64mimes:png|base64max:1024',
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
        $this->store($request, $member);

        return response([
            'status' => true,
            'message' => 'Member Updated',
        ], 200);
    }
}
