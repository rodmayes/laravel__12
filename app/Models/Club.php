<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Club extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $table = 'playtomic_club';

    public $filterable = [
        'id',
        'name',
        'playtomic_id',
        'days_min_booking',
        'timetable_summer',
        'booking_hour'
    ];

    protected $cats = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [
        'name',
        'playtomic_id',
        'days_min_booking',
        'timetable_summer','booking_hour'
    ];

    public $appends = ['number_resources'];

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getTimetableSummerActiveAttribute(){
        return $this->timetable_summer == 1;
    }

    public function getNumberResourcesAttribute(){
        return $this->resources()->count();
    }
}
