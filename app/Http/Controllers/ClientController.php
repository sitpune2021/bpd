<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Client;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{

    // Add Client 
    public function addClient(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'type' => 'required',
            'contact' => 'required',
            'address' => 'required',
            'pan' => 'required',
            'gst_certificate' => 'required|mimes:jpeg,png,jpg,gif,pdf',
            'fssi' => 'required|mimes:jpeg,png,jpg,gif,pdf',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }

        $clients = new Client;

        $clients->name = $request->name;
        $clients->type = $request->type;
        $clients->contact = $request->contact;
        $clients->address = $request->address;
        $clients->pan = $request->pan;
        $clients->user_id = auth()->id();

        if ($request->hasFile('gst_certificate')) {
            $image = $request->file('gst_certificate');

            $imageName = $image->getClientOriginalName();

            $path = public_path("upload/Gst certificate/");

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $image->move($path, $imageName);
            $clients->gst_certificate = $imageName;
        }
        if ($request->hasFile('fssi')) {
            $image = $request->file('fssi');

            $imageName = $image->getClientOriginalName();

            $path = public_path("upload/FSSI/");

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $image->move($path, $imageName);
            $clients->fssi = $imageName;
        }

        if ($clients->save()) {
            $resp['status'] = true;
            $resp['message'] = "Client added successfully!";
            return response()->json($resp, 200);

        } else {
            $resp['status'] = false;
            $resp['message'] = "Client Not added successfully!";
            return response()->json($resp, 400);
        }
    }

    // Anuradha Jamdade on 12/4/2025 @ 3:16 pm
    // Get All Client Data
    public function getAllClientData()
    {
        $page = 1;
        $perPage = 10;
        
        $client = Client::paginate(10);

        if (!$client) {
            $resp['status'] = false;
            $resp['message'] = "No Client Found!";
            return response()->json($resp, 200);
        } else {
            $resp['status'] = true;
            $resp['message'] = "Clients found";
            $resp['data'] = $client;
            return response()->json($resp, 400);
        }

    }

    // Get Single Client Data
    public function getSingleClient($id)
    {

        if ($id) {
            $data = Client::findOrFail($id)->first();

            if (!$data) {
                $resp['status'] = false;
                $resp['message'] = "Client found";
                return response()->json($resp,404);
            }

            $resp['status'] = true;
            $resp['message'] = "Clients found";
            $resp['data'] = $data;
            return response()->json($resp,200);
        } else {
            $resp['status'] = false;
            $resp['message'] = "Invalid Id";
            return response()->json($resp,402);
        }

    }
}
