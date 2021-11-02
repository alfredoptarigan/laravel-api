<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\Post;
use App\Utils\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\APITraits;
use App\Traits\HistoryTrait;

class AdminController extends Controller
{
    use APITraits, HistoryTrait;

    public function histories(Request $request)
    {
        $responseSuccess = $this->response("Success", [
            'history' => $request->user()->histories
        ]);
        return APIResponse::SuccessResponse($responseSuccess, 200);
    }

    public function deletePost(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'post_id' => 'exists:posts,id|required'
        ]);

        if ($validate->fails()) {
            $responseError = $this->response('Something Wrong', $validate->errors());
            return APIResponse::ErrorResponse($responseError, 400);
        }

        $post = Post::where('id', $request->post_id)->first();


        $post->delete();

        $history = History::create([
            'user_id' => $request->user()->id,
            'ip_address' => $request->ip(),
            'description' => $this->successHistory("menghapus post dengan nomor ID " . $post->id)
        ]);

        $responseSuccess = $this->response("Success deleted the post and checkout the history.", [
            'post' => $post,
            'history' => $history
        ]);
        return APIResponse::SuccessResponse($responseSuccess, 200);
    }


    public function editPost(Request $request)
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

        $post = Post::findOrFail($request->post_id);

        $post->title = $validate->validate()['title'];
        $post->description = $validate->validate()['description'];
        $post->save();

        $history = History::create([
            'user_id' => $request->user()->id,
            'ip_address' => $request->ip(),
            'description' => $this->successHistory("mengubah post dengan nomor ID " . $post->id)
        ]);

        $responseSuccess = $this->response("Success updated the post and checkout the history.", [
            'post' => $post,
            'history' => $history
        ]);
        return APIResponse::SuccessResponse($responseSuccess, 200);
    }
}
