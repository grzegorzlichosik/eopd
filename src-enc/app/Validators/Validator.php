<?php

namespace App\Validators;

use Illuminate\Support\Facades\Validator as ValidatorFacade;

abstract class Validator
{
    abstract public function rules(): array;

    abstract public function messages(): array;

    public function validate(array $data): array
    {
        return ValidatorFacade::make(
            $data,
            $this->rules(),
            $this->messages()
        )->validate();
    }
}
