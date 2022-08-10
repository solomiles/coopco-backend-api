<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
class CRUDController extends Controller
{
    /**
     * Admin update
     * 
     * @param Request $request
     * @return Response
     */
    public function update (Request $request) {
        
        $validate = $this->validator($request);
        if ($validate->fails()) {
            return response([
                'status' => false,
                'errors' => $validate->errors()->messages()
            ], 400);
        }

        $oldPhoto = $request->user()->photo;

        $admin = $this->store($request);

        if($oldPhoto != 'default-admin.png' && $request->input('photo')) {
            Storage::delete('/public/admins/photo/'.$oldPhoto);
        }

        return response([
            'status' => true,
            'message' => 'Admin Updated',
            'data' => $admin
        ], 200);
    }

    /**
     * validates admin update request
     * 
     * @param Request $request
     * 
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator($request) {
        return Validator::make($request->all(), [
            'name' => 'required|string',
            'username' => 'required|string|min:5',
            'password' => 'confirmed',
            'photo' => 'base64image|base64max:6000'
        ]);
    }

    /**
     * stores admin data
     * 
     * @param Admin $admin
     * @return void
     */
    public function store(Request $request) {
        $admin = $request->user();

        if($request->input('photo')) {
            // Store profile photo
            $photo = base64ToFile($request->input('photo'));
            $photoName = $photo->hashName();
            $photo->store('public/admins/photo');
        }
        else {
            $photoName = $admin->photo;
        }

        $admin->name = ucwords(strtolower($request->name));
        $admin->username = $request->username;
        if($request->password) $admin->password = Hash::make($request->password);
        $admin->photo = $photoName;
         
        $admin->save();

        return $admin;
    }
}
