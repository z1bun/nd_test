<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(Request $request)
    {
        publish('user.add', $request->json()->all());

        return response()->json(['status' => 'new']);
    }

}
