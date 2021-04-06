<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Confirmation;
use App\Models\Userdata;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use OneSignal;

class ConfirmationsController extends ApiController
{
    public function index() {
        $confirms = Confirmation::all();

        if (!$confirms) {
            return $this->sendError($confirms,"No se han encontrado confirmaciones, intentelo de nuevo mas tarde");
        }

        return $this->sendSuccess($confirms,"Estas son las confirmaciones actuales");
    }

    public function store(Request $req) {

        $validation = Validator::make($req->all(),[
            'id_user' => 'required',
            'id_activity' => 'required'
        ]);

        if ($validation->fails()) {
            return $this->sendError($validation->errors(),"No se han proporcionado los datos suficientes para la confirmacion",422);
        }

        $user_exists = Userdata::find($req->get('id_user'));

        if (!$user_exists) {
            return $this->sendError($user_exists,"No se ha encontrado un usuario con ese ID");
        }

        $current_confirmed = Confirmation::where([
            ['id_user','=',$req->get('id_user')],['id_activity','=',$req->get('id_activity')
        ]])->first();

        if ($current_confirmed) {
            return $this->sendError($current_confirmed,"El usuario ya ha confirmado su participacion en esta actividad",401);
        }

        $new_confirmation = Confirmation::create($req->all());

        // One Signal

        $users_activity = DB::table('confirmations')
        ->where('confirmations.id_activity','=',$req->get('id_activity'))
        ->join('userdatas','confirmations.id_user','userdatas.id_user')
        ->select('userdatas.id_one_signal')
        ->get();

        foreach($users_activity as $user) {
            $id = $user->id_one_signal;

            OneSignal::sendNotificationToUser("$user->name Se ha unido a $current_confirmed->name",$id);
        }

        // Return Success API response

        return $this->sendSuccess($new_confirmation,"Confirmacion aceptada");

    }

    public function show($id,Request $req) {
        $confirm = Confirmation::find($id);

        if (!$confirm) {
            return $this->sendError($confirm,"No se ha encontrado una confirmacion con ese ID");
        }

        $activity = $confirm->getActivity;
        $user = $confirm->getUser;

        if (!$activity || !$user) {
            return $this->sendError(null,"Esta confirmación contiene informacion inválida o corrompida");
        }

        $confirm_details = [
            'activity_info' => [
                'name' => $activity->name,
                'photo' => $activity->photo,
                'description' => $activity->description,
                'date' => $activity->date,
                'active' => $activity->active
            ],
            'user_info' => [
                'name' => $user->name,
                'photo' => $user->photo,
                'about' => $user->about_user,
                'age' => $user->age,
                'genre' => $user->genre
            ]
        ];

        return $this->sendSuccess($confirm_details,"Se han obtenido los datos de la confirmacion exitosamente");

    }

    public function delete(Request $req) {
        $confirm = Confirmation::find($req->get('id'));

        if (!$confirm) {
            return $this->sendError($confirm,"No se ha encontrado una confirmacion con ese ID");
        }

        $confirm->delete();

        return $this->sendSuccess($confirm,"Confirmacion eliminada");
    }

    public function userConfirms($id,Request $req) {
        $user = Userdata::find($id);

        if (!$user) {
            return $this->sendError($user,"No se ha encontrado un usuario con ese ID");
        }

        $confirms = $user->getConfirms;

        if (empty($confirms[0])) {
            return $this->sendError($confirms,"No se han encontrado confirmaciones para este usuario");
        }

        $activities = [];

        foreach($confirms as $confirm) {

            $individual_data = [
                'id' => $confirm->getActivity->id,
                'name' => $confirm->getActivity->name,
                'photo' => $confirm->getActivity->photo,
                'description' => $confirm->getActivity->description,
                'date' => $confirm->getActivity->date,
                'active' => $confirm->getActivity->active
            ];

            array_push($activities,$individual_data);
        }

        $dataRes = [
            'activities_confirmed' => $activities,
            'user_info' => [
                'name' => $user->name,
                'photo' => $user->photo,
                'about' => $user->about_user,
                'age' => $user->age,
                'genre' => $user->genre
            ]
        ];

        return $this->sendSuccess($dataRes,"Confirmaciones activas para $user->name");

    }
}
