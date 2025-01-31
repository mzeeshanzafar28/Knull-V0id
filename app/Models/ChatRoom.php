<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}