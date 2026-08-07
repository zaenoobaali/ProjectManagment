<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
         $user = $this->user();

    // 👈 عدلنا الحقل هنا ليفحص عمود role_name داخل جدول الأدوار
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
            'end_date' => ['required','date', 'after_or_equal:start_date']
        ];
    }
}
