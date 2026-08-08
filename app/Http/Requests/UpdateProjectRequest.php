<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        if ($user && $user->roles()->where('role_name', 'admin')->exists()) {
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
            'project_name' => ['required','string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required','date'],
            'end_date' => ['required','date', 'after_or_equal:start_date'],
            'status' => ['required', 'string', Rule::in(Project::STATUSES)]
        ];
    }
}
