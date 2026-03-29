<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradeEmbedding extends Model
{
    protected $fillable = [
        'name',
        'content',
        'embedding',
    ];

    protected function casts(): array
    {
        return [
            'embedding' => 'array',
        ];
    }
}
