<?php

namespace App\Http\Controllers;
use App\Models\Activity;
use Carbon\Carbon;
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
}
