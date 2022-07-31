<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Message;

class MessageController extends Controller
{
    /**
     * Sen message
     * @param Request $request
     * 
     * @return Response
     */
    public function send(Request $request)
    {
        // Validate form fields
        $validate = $this->validator($request);

        if ($validate->fails()) {
            return response([
                'status' => false,
                'errors' => $validate->errors()->messages(),
            ], 400);
        }
        
        $this->store($request);

        return response([
            'status' => true,
            'message' => 'Member Updated',
        ], 200);
    }

    /**
     * Store member data
     * @param Request $request
     *
     * @return void
     */
    public function store($request)
    {
        $user = $request->user();

        // Store message data
        $message = new Message();
        $message->subject = $request->subject;
        $message->content = $request->content;
        $message->from = $request->from;
        $message->to = $request->to;
        $message->to_id = $request->to_id;
        $message->from_id = $user->id;

        $message->save();
    }

    /**
     * Member data validator
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator($request)
    {
        $routePrefix = $request->route()->getPrefix();

        return Validator::make($request->all(), [
            'subject' => 'required|string',
            'content' => 'required|string',
            'from' => 'required|string',
            'to' => 'required|string',
            'to_id' => 'required|exists:'.$routePrefix == 'member'?'members,id':'admins,id',
        ]);
    }
}
