<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Task;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Task $task)
    {
        $project = $task->project;
        $isMember = $project->users()->where('user_id', $request->user()->id)->exists();
        if(!$isMember && $project->created_by !== $request->user()->id){
            return $this->errorResponse('You are not authorized to view this task', 403);
        }

        $comments = $task->comments()->with('user')->latest()->get();

        return $this->successResponse($comments, 'Task comments retrieved successfully');
    }

    public function store(StoreCommentRequest $request, Task $task)
    {
        $project = $task->project;
        $isMember = $project->users()->where('user_id', $request->user()->id)->exists();
        if(!$isMember && $project->created_by !== $request->user()->id){
            return $this->errorResponse('You are not authorized to comment on this task', 403);
        }

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'task_id' => $task->id,
            'content' => $request->content,
        ]);

        return $this->successResponse($comment, 'Comment created successfully', 201);
    }

    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        $project = $comment->task->project;
        $isMember = $project->users()->where('user_id', $request->user()->id)->exists();
        if(!$isMember && $project->created_by !== $request->user()->id){
            return $this->errorResponse('You are not authorized to update this comment', 403);
        }

        if($comment->user_id !== $request->user()->id){
            return $this->errorResponse('You can only update your own comments', 403);
        }

        $comment->update([
            'content' => $request->content,
        ]);

        return $this->successResponse($comment, 'Comment updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment, Request $request)
    {
        $project = $comment->task->project;
        $isMember = $project->users()->where('user_id', $request->user()->id)->exists();
        if(!$isMember && $project->created_by !== $request->user()->id){
            return $this->errorResponse('You are not authorized to delete this comment', 403);
        }

        if($comment->user_id !== $request->user()->id){
            return $this->errorResponse('You can only delete your own comments', 403);
        }

        $comment->delete();

        return $this->successResponse(null, 'Comment deleted successfully');
    }
}
