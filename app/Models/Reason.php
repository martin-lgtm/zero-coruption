<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reason extends Model
{
    use HasFactory;

        protected $fillable = ['name'];

    public function reports()
    {
        return $this->belongsToMany(Report::class, 'report_reason');
    }
}


