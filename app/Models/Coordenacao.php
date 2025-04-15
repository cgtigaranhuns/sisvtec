<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coordenacao extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'user_id','email'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
