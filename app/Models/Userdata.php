<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Userdata extends Model
{
    use HasFactory;

    public function getConfirms() {
        return $this->hasMany(Confirmation::class,"id_user");
    }
}
