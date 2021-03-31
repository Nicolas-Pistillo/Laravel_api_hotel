<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RegisterController extends ApiController
{
    public function store(Request $req) {
        $validator = Validator::make($req->all(),[
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(),"Error en la validacion de datos",422); 
        }

        $input = $req->all();
        $input['password'] = bcrypt($req->get('password'));

        $new_user = User::create($input);
        $token = $new_user->createToken('myApp')->accessToken;

        $dataRes = ['new_user' => $new_user,'token' => $token];

        return $this->sendSuccess($dataRes,"Bienvenido al sistema");

    }

    public function testOauth() { // Para obtener la informacion del usuario autenticado, necesitamos enviar el encabezado Authorization con el token bearer que se nos asigna en el momento en el que se inicia sesion
        $user = Auth::user();

        return $this->sendSuccess($user,"Este es el usuario autenticado");
    }
}
