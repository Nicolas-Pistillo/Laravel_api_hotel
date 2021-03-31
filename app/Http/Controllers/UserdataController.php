<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Userdata;

class UserdataController extends ApiController
{
    public function getUsers() {
        $users = Userdata::all();
        return $this->sendSuccess($users,"Este es el listado de usuarios");
    }
}
