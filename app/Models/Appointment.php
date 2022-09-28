<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'time',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //   appointment_history
    public function appointmentHistory()
    {
        return $this->hasMany(AppointmentHistory::class, 'appointment_id');
    }
}
