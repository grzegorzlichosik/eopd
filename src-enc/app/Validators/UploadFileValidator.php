<?php

namespace App\Validators;


use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class UploadFileValidator extends Validator
{

    public function rules(): array
    {
        return [
            'file'      => [
                'required',
                'mimes:pdf'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required'       => trans('validation.file_required'),
            'file.mimes'          => trans('validation.file_type'),
        ];
    }
}
