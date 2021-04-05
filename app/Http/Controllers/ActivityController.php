<?php

namespace App\Http\Controllers;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ActivityController extends ApiController
{
    public function index() {
        $activities = DB::table('activities')
        ->select('id','name','photo','description','date')
        ->get();

        if (!$activities) {
            return $this->sendError("No activities found","Error al recuperar datos de actividades");
        }

        return $this->sendSuccess($activities,"Estas son las actividades disponibles");
    }

    public function store(Request $req) {
        
        $validation = Validator::make($req->all(),[
            'name' => 'required|max:130|unique:activities',
            'photo' => 'required|max:300',
            'description' => 'required',
            'date' => 'required'
        ]);

        if ($validation->fails()) {
            return $this->sendError($validation->errors(),"Ocurrio un error con la validacion de datos",422);
        }

        $new_activity = Activity::create($req->all());

        return $this->sendSuccess($new_activity,"Actividad creada con éxito");
    }

    public function update(Request $req) {
        $activity = Activity::find($req->get('id'));

        if (!$activity) {
            return $this->sendError($activity,"No se ha encontrado una actividad con ese ID");
        }

        $validation = Validator::make($req->all(),[
            'name' => 'required|max:130|unique:activities',
            'photo' => 'required|max:300',
            'description' => 'required',
            'date' => 'required'
        ]);

        if ($validation->fails()) {
            return $this->sendError($validation->errors(),"Error en la validación de datos para actualizar",422);
        }

        $activity->name = $req->get('name');
        $activity->photo = $req->get('photo');
        $activity->description = $req->get('description');
        $activity->date = $req->get('date');

        $activity->update();

        return $this->sendSuccess($activity,"Actividad actualizada con éxito");

    }

    public function changeActive($id,Request $req) {
        $activity = Activity::find($id);

        if (!$activity) {
            return $this->sendError($activity,"No se ha encontrado una actividad con ese ID");
        }

        $validation = Validator::make($req->all(),[
            'active' => 'required|integer'
        ]);

        if ($validation->fails() || $req->get('active') > 1 || $req->get('active') < 0 ) {
            return $this->sendError("The field active is not valid","Error en la validacion de datos, compruebe el valor de active",403);
        }

        $activity->active = $req->get('active');

        $activity->save();

        return $this->sendSuccess($activity,"Estado de actividad modificado con éxito");
    }

    public function show($id,Request $req) {
        $activity = Activity::find($id);

        if (!$activity) {
            return $this->sendError($activity,"No se ha encontrado una actividad con ese ID");
        }

        $activity_find = DB::table('activities')
        ->select('id','name','photo','description','date')
        ->where('id','=',$id)
        ->first();

        return $this->sendSuccess($activity_find,"Actividad encontrada");
    }
}
