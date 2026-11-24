<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'prefix_image',
        'prefix_document',
    ];

    /**
     * Devolve a linha única de definições, criando-a se não existir.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'prefix_image' => 'IMG',
            'prefix_document' => 'DOC',
        ]);
    }
}
