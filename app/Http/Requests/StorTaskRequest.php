<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $projectId = $this->input('project_id'); 
        $project = Project::find($projectId);


        if ($user && 
        $user->roles()->where('role_name', 'admin')->exists() && 
        $project && 
        $project->created_by === $user->id) {
            return true; 
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         return [
        'project_id'  => ['required', 'integer', 'exists:projects,id'], 
        'title'       => ['required', 'string', 'max:255'],
        'description' => ['nullable', 'string'],
        'due_date'    => ['required', 'date', 'after_or_equal:today'], 
        //'assigned_to' => ['required', 'integer', 'exists:users,id'] 
        ];
    }
}
