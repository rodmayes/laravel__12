<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ScheduledJob extends Model
{
    protected $fillable = [
        'job_id',
        'job_type',
        'payload',
        'status',
        'scheduled_for',
        'executed_at',
        'cancelled_at'
    ];

    protected $casts = [
        'payload' => 'array',
        'scheduled_for' => 'datetime',
        'executed_at' => 'datetime',
        'cancelled_at' => 'datetime'
    ];

    public function schedulable()
    {
        return $this->morphTo();
    }

    public function scopeBySchedulable($query, $value){
        return $query->where('job_id', $value);
    }

    public function scopeBySchedulableType($query, $value){
        return $query->where('job_type', $value);
    }

    public function scopeByStatus($query, $value){
        return $query->where('status', $value);
    }

    public function getStatusAttribute(){
        return $this->attributes['status'];
    }

    public function cancel(): void
    {
        $this->update([
            'cancelled_at' => now(),
            'status' => 'cancelled',
        ]);

        if ($this->job_id && config('queue.default') === 'database') {
            DB::table('jobs')->where('uuid', $this->job_id)->delete();
        }

        // Si estás usando Redis, necesitas manejarlo tú (puedo ayudarte si es el caso)
    }

    public function markAsExecuted(bool $success = true): void
    {
        $this->update([
            'executed_at' => now(),
            'status' => $success ? 'success' : 'failed',
        ]);
    }
}

