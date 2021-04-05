<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Userdata;

class Confirmation extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'id_activity'
    ];
    
    public function getUser() {
        return $this->belongsTo(Userdata::class,"id_user");
    }

    public function getActivity() {
        return $this->belongsTo(Activity::class,"id_activity");
    }

}
