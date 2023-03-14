<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\TsmTransition;
use Illuminate\Database\Eloquent\Model;

class TsmState extends Model
{
    use SoftDeletes;

    protected $table = 'tsm_states';

    public function transitions()
    {
        return $this->hasMany(TsmTransition::class, 'from_states_id', 'id')->orderBy('id');
    }

    public static function encounterStatus(): array
    {
        return convertToDropdownData(TsmState::where('class', 'Encounter')
            ->orderBy('name')
            ->get()
            ->map(function ($item) {
                return
                    [
                        'name' => $item['name'],
                        'uuid' => $item['name'],
                    ];
            }));
    }

}
