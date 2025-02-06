<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class File extends Model
{
    protected $fillable = [
        'user_id', 'file_id', 'original_name', 
        'stored_name', 'mime_type', 'size', 'expires_at'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($file) {
            $file->file_id = Str::uuid();
            $file->expires_at = now()->addHours(24);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}