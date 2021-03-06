<?php

namespace App\Http\Requests\Backend\Music\Single;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSingleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return access()->hasRole(1);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file' => 'nullable|file',
            'category' => 'required|string',
            'genre' => 'required|string',
            'description'  => 'nullable|string'
        ];
    }
}
