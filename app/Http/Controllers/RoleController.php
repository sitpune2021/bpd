<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use Validator;
class RoleController extends Controller
{
    //

    public function create(Request $request)
    {
        $validate=Validator::make($request->all(),[

            'role'=>'required'
        ]);

        if($validate->fails())
        {
            return response()->json(['error' => $validate->errors()], 422);
        }

        $role=new Role;
        $role->role=$request->role;

        if($role->save())
        {
            return response()->json(['message'=>'Successfully inserted'],200);
        }
        else
        {
            return response()->json(['message'=>'Try Again'],200);
        }
    }
}
