<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BulkApplicationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'ids'    => ['required', 'array', 'min:1'],
            'ids.*'  => ['required', 'integer', 'exists:applications,id'],
            'action' => ['required', 'in:approve,reject'],
            'remarks'=> ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required'    => 'Please select at least one application.',
            'action.required' => 'Please choose approve or reject.',
            'action.in'       => 'Action must be approve or reject.',
        ];
    }
}

