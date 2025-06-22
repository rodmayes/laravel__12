<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Booking extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $table = 'playtomic_booking';

    const STATUS_ONTIME = 'on-time';
    const STATUS_CLOSED = 'closed';
    const STATUS_TIMEOUT = 'time-out';

    const STATUS = [
        ['id' => Booking::STATUS_ONTIME, 'name' => 'On time'],
        ['id' => Booking::STATUS_TIMEOUT, 'name' => 'Time out'],
        ['id' => Booking::STATUS_CLOSED, 'name' => 'Closed']
    ];

    const PREFERENCES = [
        ['id' => 'timetable', 'name' => 'Time preference'],
        ['id' => 'resource', 'name' => 'Resource preference']
    ];

    const DURATION = [
        ['id' => 30, 'name' => '30min'],
        ['id' => 60, 'name' => '1h'],
        ['id' => 90, 'name' => '1h 30h'],
        ['id' => 120, 'name' => '2h'],
        ['id' => 150, 'name' => '2h 30h'],
        ['id' => 180, 'name' => '3h']
    ];

    public $orderable = [
        'id',
        'name',
        'playtomic_id',
        'club_id',
        'resources',
        'timetables',
        'status',
        'started_at',
        'timetable_id',
        'created_by',
        'public',
        'booked_at',
        'player',
        'duration'
    ];

    public $filterable = [
        'id',
        'name',
        'playtomic_id',
        'club_id',
        'resources',
        'status',
        'started_at',
        'timetables',
        'created_by',
        'public',
        'booking_preference',
        'booked_at',
        'player',
        'duration'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'started_at',
        'booked_at'
    ];

    protected $fillable = [
        'id',
        'name',
        'playtomic_id',
        'club_id',
        'resources',
        'timetables',
        'status',
        'timetable_id',
        'started_at',
        'created_by',
        'public',
        'booking_preference',
        'booked_at',
        'player_email',
        'duration'
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function player()
    {
        return $this->hasOne(User::class, 'email', 'player_email');
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function timetable()
    {
        return $this->belongsTo(Timetable::class);
    }

    public function scheduledJobs(){
        return $this->hasMany(ScheduledJob::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getIsOnTimeAttribute(){
        return $this->status === 'on-time';
    }

    public function getIsBookedAttribute(){
        return !is_null($this->booked_at);
    }

    public function getPlayerNameAttribute(){
        return $this->player->name;
    }

    public function scopeByClub($query, $value){
        return $query->where('club_id', $value)
            ->orWhere('public', 1);
    }

    public function scopeByUser($query, $value){
        return $query->where('created_by', $value);
    }

    public function scopeOnTime($query){
        return $query->where('status', 'on-time');
    }

    public function scopeNotClosed($query){
        return $query->where('status', '<>', 'closed');
    }

    public function scopeBooked($query){
        return $query->whereNotNull('booked');
    }

    public function scopeNoBooked($query){
        return $query->whereNull('booked');
    }

    public function scopeByPlayer($query, $value){
        return $query->whereRaw("LOWER(player_email) like '%". mb_strtolower(trim($value))."%'");
    }

    public function scopeIsPublic($query){
        return $query->where('public',1);
    }

    public function scopeOnDate($query){
        return $query->where('started_at','>=',Carbon::now());
    }

    public function setStatusTimeOut(){
        $this->status = 'time-out';
        return $this->save();
    }

    public function setStatusClosed(){
        $this->status = 'closed';
        return $this->save();
    }

    public function setStatusOnTime(){
        $this->status = 'on-time';
        return $this->save();
    }

    public function setBooked(){
        $this->booked_at = now();
        return $this->save();
    }

    public function toggleBooked(){
        $this->booked_at = is_null($this->booked_at) ? now() : null;
        return $this->save();
    }

    public function getExecutionDateAttribute(){
        $timezone = env('APP_DATETIME_ZONE', 'Europe/Andorra');
        $executionDate = $this->started_at
            ->copy()
            ->setTimezone($timezone)
            ->subDays((int) $this->club->days_min_booking)
            ->setTimeFromTimeString($this->club->booking_hour);

        return $executionDate ?: null;
    }
}
