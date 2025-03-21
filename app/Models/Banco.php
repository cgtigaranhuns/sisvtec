<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banco extends Model
{
    use HasFactory;

    protected $fillable = ['numero','nome'];

    public function discente()
    {
        return $this->hasMany(Discente::class);
    }
}
