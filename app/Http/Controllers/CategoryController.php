<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Validator;

class CategoryController extends Controller
{
    //
    public function addCategory(Request $request)
    {
        $validate = Validator::make($request->all(), [

            'name' => 'required',
            'price' => 'required',
           
        ]);
        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }
    
        $clients = new Category();
    
        $clients->name = $request->name;
        $clients->price = $request->price;
      
        if($clients->save())
        {
            $resp['status'] = true;
            $resp['message'] = "Client added successfully!";
            return response()->json($resp);
        }
        else {
            $resp['status'] = false;
            $resp['message'] = "Client Not added successfully!";
            return response()->json($resp);
        }
    }

    public function searchCategory(Request $request)
    {
        $query = Category::query(); 

        if ($request->has('name') && !empty($request->name)) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        $results = $query->paginate(10); 
        
        return response()->json([
            'status' => true,
            'message' => 'Categories fetched successfully',
            'data' => $results
        ]);
    }
}
