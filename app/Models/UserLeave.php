<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserLeave extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'leave_type',
        'description',
    ];


    /**
     * User who applied for leave.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}