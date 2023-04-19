<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Appointment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'appointments';

    protected $fillable = [
        'name',
        'start_time',
        'end_time'
    ];

    public function serviceProvider(){
        return $this->belongsTo(ServiceProvider::class, 'service_provider_id');
    }
}
