<?php

declare(strict_types=1);

namespace AngeArsene\Chat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Conversation extends Model
{
    public function participants(): HasMany
    {
        return $this->hasMany(Participation::class);
    }
}