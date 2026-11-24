<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'category',
        'original_name',
        'file_name',
        'mime_type',
        'size',
        'path',
        'url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categoryRef()
    {
        return $this->belongsTo(Category::class, 'category', 'slug');
    }

    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    public function isPdf(): bool
    {
        return str_contains($this->mime_type, 'pdf');
    }

    public function sizeHuman(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, $i === 0 ? 0 : 1) . ' ' . $units[$i];
    }
}
