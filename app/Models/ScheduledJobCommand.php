<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduledJobCommand extends Model
{
    protected $fillable = [
        'command',
        'parameters',
        'scheduled_for',
        'output',
        'active'
    ];

    protected $casts = [
        'parameters' => 'array',
        'scheduled_for' => 'string',
        'active' => 'boolean'
    ];
}

