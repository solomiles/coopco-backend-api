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

        return response([
            'status' => true,
            'message' => 'Fetch Successful',
            'data' => $sentMessages
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

        return response([
            'status' => true,
            'message' => 'Fetch Successful',
            'data' => $receivedMessages
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
        $message = $this->getSingleSentMessage($messageId, $request->user());

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

    /**
     * Get single received message
     * 
     * @param int $messageId
     * @param object $user
     */
    public function getSingleReceivedMessage($messageId, $user) {
        $modelName = substr($user->getTable(), 0, -1);

        return Message::where([['id', '=', $messageId], ['to_id', '=', $user->id], ['to', '=', $modelName]]);
    }

    /**
     * Get single sent message
     * 
     * @param int $messageId
     * @param object $user
     */
    public function getSingleSentMessage($messageId, $user) {
        $modelName = substr($user->getTable(), 0, -1);

        return Message::where([['id', '=', $messageId], ['from_id', '=', $user->id], ['from', '=', $modelName]]);
    }

    /**
     * Mark received message as seen
     * 
     * @param Request $request - Request object
     * @param int $messageId
     * 
     * @return Response
     */
    public function markAsSeen(Request $request, $messageId)
    {
        $message = $this->getSingleReceivedMessage($messageId, $request->user());

        if ($message->count() < 1) {
            return response([
                'status' => false,
                'errors' => g('FORBIDDEN'),
            ], 403);
        }

        $message->update(['seen' => true]);

        return response([
            'status' => true,
            'message' => 'Updated Successfuly',
        ], 200);
    }

    /**
     * Mark received message as read
     * 
     * @param Request $request - Request object
     * @param int $messageId
     * 
     * @return Response
     */
    public function markAsRead(Request $request, $messageId)
    {
        $message = $this->getSingleReceivedMessage($messageId, $request->user());
        
        if ($message->count() < 1) {
            return response([
                'status' => false,
                'errors' => g('FORBIDDEN'),
            ], 403);
        }

        $message->update(['read' => true]);

        return response([
            'status' => true,
            'message' => 'Updated Successfuly',
        ], 200);
    }
}
