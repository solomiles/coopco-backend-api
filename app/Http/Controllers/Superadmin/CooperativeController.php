<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Cooperative;

class CooperativeController extends Controller
{
    /**
     * Create new cooperative
     * 
     * @param Request $request
     * 
     * @return Response 
     */
    public function create(Request $request){
        // Validate input fields
        $validate = $this->validator($request);

        if ($validate->fails()) {
            return response([
                'status' => false,
                'errors' => $validate->errors()->messages(),
            ], 400);
        }

        // Switch and migrate new schema
        $this->switchMigrateSchema($request);

        // Store data for new cooperative
        $this->store($request);

        return response([
            'status' => true,
            'message' => 'Post Created Successfully',
        ], 200);
    }

    /**
     * News data validator
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator($request)
    {
        return Validator::make($request->all(), [
            'logo' => 'sometimes|base64image|base64mimes:png,jpg,jpeg|base64max:6000',
            'name'=>'required',
            'country'=>'required',
            'plan'=>'required',
            'domain_name'=>'required',
            'content' => 'required'
        ]);
    }

    /**
     * Create and migrate schema
     * 
     * @param Request $request
     * 
     * @return void
     */
    public function switchMigrateSchema(Request $request){
        // Create new schema
        $schemaName = str_replace('.', '_', $request->domain_name);
        createSchema($schemaName);

        // Switch and migrate new schema
        migrateNewSchema($schemaName);
    }

    /**
     * Store cooperative data
     * @param Request $request
     * @param Cooperative $cooperative
     *
     * @return void
     */
    public function store($request, Cooperative $cooperative)
    {
        if($request->input('logo')) {
            // Store cooperative logo
            $photo = base64ToFile($request->input('logo'));
            $photoName = $photo->hashName();
            $photo->store('public/cooperative/logo');
        }

        // Cooperative Data
        $cooperative->name = ucfirst($request->name);
        $cooperative->description = $request->description;
        $cooperative->country = $request->country;
        $cooperative->plan = $request->plan;
        $cooperative->domain_name = $request->domain_name;
        $cooperative->logo = isset($photoName)?$photoName:null;

        $cooperative->save();
    }
}
