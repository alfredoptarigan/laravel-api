<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;

use App\Traits\APITraits;
use App\Traits\HistoryTrait;
use App\Utils\APIResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
    use APITraits, HistoryTrait;

    public function index()
    {
        $tag = Tag::all();

        $responseSuccess = $this->response(
            "Success get tag",
            $tag,
        );

        return APIResponse::SuccessResponse($responseSuccess, 200);
    }

    public function create(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'tag' => 'required|string'
        ]);

        if ($validate->fails()) {
            $responseError = $this->response('Something Wrong', $validate->errors());
            return APIResponse::ErrorResponse($responseError, 400);
        }

        $tag = Tag::create([
            'tag' => $validate->validate()['tag']
        ]);


        $history = History::create([
            'user_id' => $request->user()->id,
            'ip_address' => $request->ip(),
            'description' => $this->successHistoryUser("menambahkan tag dengan judul " . $tag->tag)
        ]);

        $responseSuccess = $this->response("Success added tag and checkout the history, thankyou.", [
            'tag' => $tag,
            'history' => $history
        ]);

        return APIResponse::SuccessResponse($responseSuccess, 200);
    }

    public function addTagToPost(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id',
            'tag_id' => 'required|exists:tags,id'
        ]);

        if ($validate->fails()) {
            $responseError = $this->response('Something Wrong', $validate->errors());
            return APIResponse::ErrorResponse($responseError, 400);
        }

        $post = Post::findOrFail($request->post_id);
        $tag = Tag::findOrFail($request->tag_id);

        if (Auth::user()->id != $post->user_id) {
            $responseError = $this->response("You don't have privilege to use this feature.", []);
            return APIResponse::ErrorResponse($responseError, 401);
        }

        $post->tags()->attach($validate->validate()['tag_id']);

        $responseSuccesful = $this->response(
            'Successfully Adding Tag to Post',
            [
                'post' => $post,
                'history' => $this->postHistoryUser($request,  "menambahkan tag dengan judul ", $tag->tag)
            ]
        );

        return APIResponse::SuccessResponse($responseSuccesful, 200);
    }
}
