<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{

    public function sendSuccess($data,$message,$code=200) {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];
        return response()->json($response,$code);
    }

    public function sendError($error,$message,$code=404) {
        $response = [
            'success' => false,
            'error_message' => $message,
            'error_data' => $error
        ];
        return response()->json($response,$code);
    }

}
