<?php

namespace App\Validators;

use App\Models\Flow;
use Illuminate\Validation\Rule;

class UpdateFlowValidator extends CreateFlowValidator
{
    public function rules(): array
    {
        $flow = Flow::whereUuid(request()->route('uuid'))->first();

        $ruleUnique = Rule::unique('flows', 'name')
            ->where('organisations_id', auth()->user()->organisations_id);
        if ($flow) {
            $ruleUnique = $ruleUnique->ignoreModel($flow);
        }

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                $ruleUnique,
            ],
            'objective' => [
                'required',
                'string',
                'min:3',
                'max:255'
            ],
            'channels'  => 'required|array'
        ];
    }
}
