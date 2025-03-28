<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Role extends Model
{
    use HasFactory; 
    

    protected $fillable = [
        'name',
        'guard_name',
    ];

    // public function permissions()
    // {
    //     return $this->belongsToMany(Permission::class);
    // }

//    public function getActivitylogOptions(): LogOptions
//     {
//         return LogOptions::defaults()
//          ->logOnly(['*'])
//          ->logOnlyDirty();
//         // Chain fluent methods for configuration options
//     } 
}
