<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Category;
class InvoiceController extends Controller
{
    //Anuradha Jamdade on 12/4/2025 @ 4:00PM

   
    //Create Invoice
    public function createInvoice(Request $request)
    {
        $category = $request->category;
        $quantity = $request->quantity;

        if (!$category) {
            $resp['status'] = false;
            $resp['message'] = "Category not selected";
            return response()->json($resp);
        }

        $categoryData = Category::findOrFail($category)->first();
        $price = $categoryData->price;

        $cdata = new Invoice();
        $cdata->client_id = $request->client_id;
        $cdata->category = $category;
        $cdata->quantity = $quantity;
        $total = $quantity * $price;
        $cdata->total_price = $total;

        $cdata->payment_method = $request->payment_method;
        if ($cdata->payment_method == "online") {
            if ($request->hasFile('screenshot')) {
                $image = $request->file('screenshot');

                $imageName = $image->getClientOriginalName();

                $path = public_path("upload/Invoices/");

                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                $image->move($path, $imageName);
                $cdata->screenshot = $imageName;
            }

        } else {
            if ($cdata->payment_method == "cash") {
                if ($request->hasFile('screenshot')) {
                    $image = $request->file('screenshot');

                    $imageName = $image->getClientOriginalName();

                    $path = public_path("upload/Cash Receipt/");

                    if (!file_exists($path)) {
                        mkdir($path, 0777, true);
                    }

                    $image->move($path, $imageName);
                    $cdata->screenshot = $imageName;
                }
            } else {
                if ($request->hasFile('screenshot')) {
                    $image = $request->file('screenshot');

                    $imageName = $image->getClientOriginalName();

                    $path = public_path("upload/Cheque/");

                    if (!file_exists($path)) {
                        mkdir($path, 0777, true);
                    }

                    $image->move($path, $imageName);
                    $cdata->screenshot = $imageName;
                }
            }
        }

        if ($cdata->save()) {
            $resp['status'] = true;
            $resp['message'] = "Invoice Generated Successfully";
            return response()->json($resp);
        } else {
            $resp['status'] = false;
            $resp['message'] = "Invoice not Generated";
            return response()->json($resp);
        }
    }

    // get invoice data

    public function getInvoiceDetails($id)
    {
        $data = Invoice::findOrFail($id);
        $resp=[];
        if ($data) {
            $client_data = Client::findOrFail($data['client_id']);

            $category_data=Category::findOrFail($data['category']);

            $data->created_at = date('d-m-Y', strtotime($data->created_at));

            if ($client_data) {
                // $resp['status'] = true;
                // $resp['message'] = "Invoice Details";
                // $resp['invoice_data'] = $data;
                // $resp['client_data'] = $client_data;
                // $resp['category_data']= $category_data;

                return response()->json(
                    ['message'=>"Invoice Details",
                'invoice_data'=>$data,
                'client_data'=>$client_data,
                'category_data'=>$category_data
                ],200);
            } else {
                // $resp['status'] = 400;
                // $resp['message'] = "Client details Not Found";
                return response()->json(['message'=>"Invoice Details",
                'invoice_data'=>$data,
                'client_data'=>$client_data,
                'category_data'=>$category_data
                ],200);
            }
        } else {
            $resp['status'] = 400;
            $resp['message'] = "Invoice Not Found";
            return response()->json($resp);
        }
    }

}
