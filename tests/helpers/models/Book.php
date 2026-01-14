<?php

declare(strict_types=1);

namespace AngeArsene\Chat\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use AngeArsene\Chat\Tests\Database\Factories\BookFactory;

class Book extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    protected $fillable = ['name'];

    protected static function newFactory()
    {
        return BookFactory::new();
    }
}
