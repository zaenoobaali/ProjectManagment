<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StorProjectRequest;
use App\Models\Project;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddMemberToProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\User;

class ProjectController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $projects = $request->user()->projects()->latest()->get();

        return $this->successResponse($projects,'Projects retrieved successfully');
        
    }

    public function store(StorProjectRequest $request)
    {
        $project = Project::create([
            'project_name' => $request->project_name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => Project::STATUS_ACTIVE,
            'created_by' => $request->user()->id
        ]);
        
        $request->user()->projects()->attach($project->id);
        return $this->successResponse($project,'Project created successfuly',201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Project $project)
    {
        //
        if(!$project){
            return $this->errorResponse('Project not found', 404);
        }
        $isMember = $project->members()->where('user_id', $request->user()->id)->exists();
    
        if (!$isMember && $project->created_by !== $request->user()->id) {
            return $this->errorResponse('You are not authorized to view this project', 403);
        }

        $project->load(['createdBy', 'members', 'tasks']);
            return $this->successResponse($project,'Project showed successfuly');
    }

    public function addMemberToProject(AddMemberToProjectRequest $request)
    {
        //$user = User::findOrFail($request->user_id);
        $project = Project::findOrFail($request->project_id);
        if ($project->members()->where('user_id', $request->user_id)->exists()) {
            return $this->errorResponse('This user already belongs to the project', 400);
        }
        $project->members()->attach($request->user_id);
        return $this->successResponse(null,'Member added to project successfully');
    }
    
 
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project->update($request->validated());

         return $this->successResponse($project, 'Project updated successfully', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, Request $request)
    {
        if ($project->created_by !== $request->user()->id || 
        !$request->user()->roles()->where('role_name', 'admin')->exists()) {
            return $this->errorResponse('You are not authorized to delete this project', 403);
        }
        $project->members()->detach();
        $project->delete();

        return $this->successResponse(null, 'Project and its memberships deleted successfully', 200);
    }
}
