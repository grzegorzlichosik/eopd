<?php

namespace App\Models;

use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Threadable\StateMachine\Components\StateableModelInterface;
use Threadable\StateMachine\Components\StateableModelTrait;

class EncounterAttendee extends Model
{
    use HasFactory;
    use UuidTrait;

    protected $fillable = [
        'encounters_id',
        'name',
        'email',
        'phone_number',
        'is_original',
        'is_accepted',
    ];

    protected $hidden = [
        'id',
        'encounters_id',
    ];

    protected $casts = [
        'is_original' => 'boolean',
        'is_accepted' => 'boolean',
    ];

    public function encounter(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Encounter::class, 'encounters_id', 'id');
    }
}
