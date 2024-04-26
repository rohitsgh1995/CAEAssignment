<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RosterFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:pdf,xlsx,csv,html|max:5120',
        ];
    }

    public function messages()
    {
        return [
            'file.required' => 'File is required.',
            'file.file' => 'File must be a file.',
            'file.mimes' => 'File must be of type: .pdf, .xlsx, .csv, .html',
            'file.max' => 'File must not be greater than 5MB.'
        ];
    }
}
