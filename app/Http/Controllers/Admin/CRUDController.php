<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
class CRUDController extends Controller
{
    /**
     * Admin update
     * 
     * @param Request $request
     * @param int $adminId Admin Id
     * @return Response
     */
    public function update (Request $request, int $adminId) {
        
        $validate = $this->validates($request);
        if ($validate->fails()) {
            return response([
                'status' => false,
                'errors' => $validate->errors()->messages()
            ], 400);
        }

        $admin = Admin::findOrfail($adminId);
        $this->store($request,$admin);

        return response([
            'status' => true,
            'message' => 'Admin Updated'
        ], 200);
    }

    /**
     * validates admin update request
     * 
     * @param Request $request
     * 
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validates($request) {

        return Validator::make($request->all(), [
            'name' => 'required|string',
            'username' => 'required|string|min:5',
            'password' => 'required|confirmed|min:4',
            'photo' => 'required|base64image|base64max:5024'
        ]);
    }

    /**
     * stores admin data
     * 
     * @param Admin $admin
     * @return void
     */
    public function store(Request $request, $admin) {
        
        $photo = base64ToFile($request->input('photo'));
        $photoName = $photo->hashName();
        $photoPath = $photo->store('public/admin/photo');

        $admin->name = ucwords(strtolower($request->name));
        $admin->username = $request->username;
        $admin->password = Hash::make($request->password);
        $admin->photo = $photoName;
         
        $admin->save();
    }
}
