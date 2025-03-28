<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Spatie\Activitylog\LogOptions;
// use Spatie\Activitylog\Traits\LogsActivity;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'guard_name',
    ];

    // public function roles()
    // {
    //     return $this->belongsToMany(Role::class);
    // }

    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()
    //      ->logOnly(['*'])
    //      ->logOnlyDirty();
    //     // Chain fluent methods for configuration options
    // } 
}

