<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use Illuminate\Support\Facades\Validator;

class NewsController extends Controller
{
    /**
     * Create news posts
     * 
     * @param Request $request
     * 
     * @return void
     */
    public function create(Request $request){
        // Validate form fields
        $validate = $this->validator($request);

        if ($validate->fails()) {
            return response([
                'status' => false,
                'errors' => $validate->errors()->messages(),
            ], 400);
        }

        $news = new News();
        $news->title = $request->title;
        $news->content = $request->content;
        $news->save();
    }

    /**
     * Member data validator
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator($request)
    {
        return Validator::make($request->all(), [
           'title'=>'required',
           'content'=>'required'
        ]);
    }
}
