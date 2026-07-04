<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Timetrack extends Model
{
    use HasFactory;

	use SoftDeletes;

    protected $fillable = ['description', 'user_id', 'project_id', 'start_time', 'end_time'];
    
    protected $table = "timetracks";

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class, 'project_id');
    }
}
