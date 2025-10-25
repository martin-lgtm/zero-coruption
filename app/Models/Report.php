<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sector;
use App\Models\Good;
use App\Models\Reason;

class Report extends Model
{
    use HasFactory;

protected $fillable = [
    'municipality_id',
    'gender',
    'age_range',
    'bribe_requested',
    'bribe_offered',
    'felt_choice',
    'admin_rank',
    'story',
];



        public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function sectors()
    {
        return $this->belongsToMany(Sector::class, 'report_sector');
    }

    public function goods()
    {
        return $this->belongsToMany(Good::class, 'report_good');
    }

    public function reasons()
    {
        return $this->belongsToMany(Reason::class, 'report_reason');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
