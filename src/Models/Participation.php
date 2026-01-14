<?php

declare(strict_types=1);

namespace AngeArsene\Chat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Participation extends Model
{
    protected $fillable = [
        'conversation_id',
        'messageable_id',
        'messageable_type'
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
}