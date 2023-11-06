<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invetation_code extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'code',
        'user_id'
    ];

    public $timestamps = false;

    // relationships
    public function user_owner()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

}
