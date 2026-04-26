<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder; 

class ChatRoom extends Model
{
    protected $fillable = [
        'name',
        'description',
        'max_members',
        'is_ephemeral',
        'self_destruct_hours'
    ];

    protected $casts = [
        'is_ephemeral' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function getDestructionTimeAttribute()
    {
        return $this->created_at->addHours($this->self_destruct_hours);
    }

    public function scopeSearch(Builder $query, ?string $search = null): Builder
    {
        return $query->when($search, function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }
}