<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Population extends Model
{
    protected $fillable = [
        'quantity',
        'biomassa',
        'waktu'
    ];
}
