<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StorProjectRequest;
use App\Models\Project;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProjectController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $projects = $request->user()->with('projects')->latest()->get();

        return $this->successResponse($projects,'Projects geted successfuly');
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
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
    public function show($id)
    {
        //
        $project = Project::findOrfail($id);

        return $this->successResponse($project,'Project showed successfuly');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        //
    }
}
