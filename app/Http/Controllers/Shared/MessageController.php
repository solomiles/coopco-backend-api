<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
            'message' => 'Message Sent Successfully',
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
        $tableName = $request->user()->getTable();

        return Validator::make($request->all(), [
            'subject' => 'required|string',
            'content' => 'required|string',
            'from' => 'required|string',
            'to' => 'required|string',
            'to_id' => 'required|exists:' . $tableName . ',id',
        ]);
    }

    /**
     * Get messages sent by the logged-in user
     * 
     * @param Request $request - Request object
     * 
     * @return Response - Response object
     */
    public function getSent(Request $request)
    {
        $modelName = substr($request->user()->getTable(), 0, -1);
        $userId = $request->user()->id;
        $relationship = ($modelName == 'member') ? 'toAdmin' : 'toMember';

        $sentMessages = Message::with($relationship)->where([['from_id', '=', $userId], ['from', '=', $modelName]])->orderBy('created_at', 'desc')->get();

        $data = compact('sentMessages');

        return response([
            'status' => true,
            'message' => 'Fetch Successful',
            'data' => $data['sentMessages']
        ], 200);
    }

    /**
     * Get messages received by the logged-in user
     * 
     * @param Request $request - Request object
     * 
     * @return Response - Response object
     */
    public function getReceived(Request $request)
    {
        $modelName = substr($request->user()->getTable(), 0, -1);
        $userId = $request->user()->id;
        $relationship = ($modelName == 'member') ? 'fromAdmin' : 'fromMember';

        $receivedMessages = Message::with($relationship)->where([['to_id', '=', $userId], ['to', '=', $modelName]])->orderBy('created_at', 'desc')->get();

        $data = compact('receivedMessages');

        return response([
            'status' => true,
            'message' => 'Fetch Successful',
            'data' => $data['receivedMessages']
        ], 200);
    }

    /**
     * Delete message
     * 
     * @param Request $request - Request object
     * 
     * @return Response
     */
    public function delete(Request $request, $messageId)
    {
        $user = $request->user();
        $modelName = substr($user->getTable(), 0, -1);

        $message = Message::where([['id', '=', $messageId], ['from_id', '=', $user->id], ['from', '=', $modelName]]);
        
        if ($message->count() < 1) {
            return response([
                'status' => false,
                'errors' => g('NOT_FOUND'),
            ], 404);
        }

        $message->forceDelete();

        return response([
            'status' => true,
            'message' => 'Deleted Successfuly',
        ], 200);
    }
}
