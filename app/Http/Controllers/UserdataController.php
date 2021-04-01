<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Userdata;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserdataController extends ApiController
{
    public function index() {

        $users = DB::table('users')
        ->join('userdatas','users.id','=','userdatas.id_user')
        ->select('users.id','userdatas.name','userdatas.photo','userdatas.about_user','userdatas.age','userdatas.genre')
        ->get();

        return $this->sendSuccess($users,"Este es el listado de usuarios");
    }

    public function show($id,Request $req) {

        $user = DB::table('users')
        ->join('userdatas','users.id','=','userdatas.id_user')
        ->where('users.id','=',$id)
        ->select('users.id','userdatas.name','userdatas.photo','userdatas.about_user','userdatas.age','userdatas.genre')
        ->first();

        if (!$user) {
            return $this->sendError("NULL given in the find of user","No se ha encontrado un usuario con ese ID");
        } else {
            return $this->sendSuccess($user,"Usuario encontrado");
        }

    }

    public function store(Request $req) {

        $validator = Validator::make($req->all(),[
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'age' => 'required',
            'about_user' => 'required',
            'genre' => 'required|in:male,famale,undefined'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(),"Error en la validacion de datos",422); 
        }

        $input = $req->all();
        $input['password'] = bcrypt($req->get('password'));

        $new_user = User::create($input);
        $token = $new_user->createToken('myApp')->accessToken;

        $userData = new Userdata();

        $userData->id_user = $new_user->id;
        $userData->name = $req->get('name');
        $userData->about_user = $req->get('about_user');
        $userData->photo = $req->get('photo');
        $userData->age = $req->get('age');
        $userData->genre = $req->get('genre');

        $userData->save();

        $dataRes = ['user' => $new_user,'user_info' => $userData, 'token' => $token];

        return $this->sendSuccess($dataRes,"Usuario registrado con éxito");
    }

    public function update(Request $req) {

        $user = User::find($req->get('id'));

        if (!$user) {
            return $this->sendError("NULL given in the find of user","El usuario no existe"); 
        }

        $validator = Validator::make($req->all(),[
            'name' => 'required',
            'age' => 'required',
            'about_user' => 'required',
            'genre' => 'required|in:male,famale,undefined'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(),"Error en la validacion de datos",422); 
        }

        $user->name = $req->get('name');

        $user->save();

        $userData = Userdata::where('id_user',$req->get('id'))->first();

        $userData->name = $req->get('name');
        $userData->age = $req->get('age');
        $userData->about_user = $req->get('about_user');
        $userData->genre = $req->get('genre');

        $userData->save();

        $dataRes = ['user' => $user,'user_info' => $userData];

        return $this->sendSuccess($dataRes,"Datos actualizados correctamente");
    }

    public function destroy(Request $req) {

        $user = User::find($req->get('id'));

        if (!$user) {
            return $this->sendError("NULL given in the find of user","El usuario no existe");
        }

        $user->delete();

        return $this->sendSuccess("Successful delete","Usuario eliminado exitosamente");
    }
}
