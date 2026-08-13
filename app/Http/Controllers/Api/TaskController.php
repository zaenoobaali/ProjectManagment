<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorTaskRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Traits\ApiResponse;

class TaskController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tasks = $request->user()->tasks()->latest()->get();

        if ($tasks->isEmpty()) {
            return $this->successResponse([], 'No tasks found', 200);
        }

        return $this->successResponse($tasks,'Tasks retrieved successfully');
        
    }


    public function store(StorTaskRequest $request)
    {
        $task = Task::create([
            'project_id' => $request->project_id,
            'title' => $request->title,
            'description' => $request->description,
            'status' => Task::STATUS_To_do,
            'due_date' => $request->due_date
        ]);
        

        return $this->successResponse($task,'Task created successfully',201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Task $task)
    {
        //
        if(!$task){
            return $this->errorResponse('Task not found', 404);
        }

        $project = Project::findOrFail($task->project_id);

        $isMember = $project->members()->where('user_id', $request->user()->id)->exists();

        if (!$isMember && $project->created_by !== $request->user()->id) {
            return $this->errorResponse('You are not authorized to view this task', 403);
        }

        $task->load(['project', 'users']);

        return $this->successResponse($task, 'Task details retrieved successfully');        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function assignTask(Request $request)
    {
        //
        $request->validate([
        'task_id' => 'required|integer|exists:tasks,id',
        'user_id' => 'required|integer|exists:users,id'
        ]);

        $task = Task::findOrFail($request->task_id);
        $project = Project::findOrFail($task->project_id);
        //chack project creator?
        if ($request->user()->id !== $project->created_by) {
        return $this->errorResponse('You are not authorized to assign tasks for this project', 403);
        }
        //the user is member in project?
        $isMember = $project->members()->where('user_id', $request->user_id)->exists();
        if (!$isMember) {
        return $this->errorResponse('The user is not a member of the project', 403);
        }  
        
        $task->users()->sync([$request->user_id]);

        return $this->successResponse($task->load('users'), 'Task assigned to member successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task = Task::findOrFail($request->task_id);
        
        $project = Project::findOrFail($task->project_id);
        if ($request->user()->id !== $project->created_by ||
        !$request->user()->roles()->where('role_name', 'admin')->exists()) {
        return $this->errorResponse('You are not authorized to update this task ', 403);
        }

        $task->update($request->validated());

        return $this->successResponse($task, 'Task updated successfully', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Task $task)
    {
        $task = Task::findOrFail($request->task_id);
        $project = Project::findOrFail($task->project_id);
        if ($request->user()->id !== $project->created_by ||
        !$request->user()->roles()->where('role_name', 'admin')->exists()) {
        return $this->errorResponse('You are not authorized to delete this task ', 403);
        }

        $task->users()->detach();
        $task->delete();

        return $this->successResponse(null, 'Task and its assignments deleted successfully', 200);
    }
}
