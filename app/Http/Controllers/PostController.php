<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Utils\APIResponse;
use App\Traits\APITraits;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Traits\HistoryTrait;

class PostController extends Controller
{
    use APITraits, HistoryTrait;

    public function index()
    {
        $posts = Post::with(['user', 'tags'])->get();
        $response = $this->response('Success', [
            'posts' => $posts,
        ]);

        return APIResponse::SuccessResponse($response);
    }

    public function post(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'tag_id' => 'exists:tags,id'
        ]);

        if ($validate->fails()) {
            $responseError = $this->response('Something Wrong', $validate->errors());
            return APIResponse::ErrorResponse($responseError, 400);
        }

        $post = Post::create(array_merge($validate->validate(), ['user_id' => $request->user()->id]));

        $post->tags()->attach($validate->validate()['tag_id']);

        $responseSuccesful = $this->response(
            'Successfully Publish Post',
            [
                'post' => $post,
                'history' => $this->postHistoryUser($request,  "menambahkan post dengan judul ", $post->title)
            ]
        );

        return APIResponse::SuccessResponse($responseSuccesful, 200);
    }

    public function delete(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'post_id' => 'exists:posts,id'
        ]);

        if ($validate->fails()) {
            $responseError = $this->response('Something Wrong', $validate->errors());
            return APIResponse::ErrorResponse($responseError, 400);
        }

        $post = Post::where('id', $request->post_id)->first();

        // Checking up Privilege
        if ($post->user_id != $request->user()->id) {
            $responseError = $this->response("You don't have privilege to use this feature.", []);
            return APIResponse::ErrorResponse($responseError, 401);
        }

        $post->delete();

        $responseSuccess = $this->response("Success delete the post", []);
        return APIResponse::SuccessResponse($responseSuccess, 200);
    }

    public function edit(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'post_id' => 'exists:posts,id',
            'title' => 'required|string',
            'description' => 'required|string'
        ]);

        if ($validate->fails()) {
            $responseError = $this->response('Something Wrong', $validate->errors());
            return APIResponse::ErrorResponse($responseError, 400);
        }

        $post = Post::where('id', $request->post_id)->first();

        // Checking up Privilege
        if ($post->user_id != $request->user()->id) {
            $responseError = $this->response("You don't have privilege to use this feature.", []);
            return APIResponse::ErrorResponse($responseError, 401);
        }

        $post->title = $validate->validate()['title'];
        $post->description = $validate->validate()['description'];
        $post->save();

        $responseSuccess = $this->response("Success updated the post", $post);
        return APIResponse::SuccessResponse($responseSuccess, 200);
    }
}
