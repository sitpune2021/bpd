<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
class LoginController extends Controller
{
    
    public function register(Request $request)
    {
        $resp = [];
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'mobile' => 'required|digits:10',
            'role' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 400);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->role = $request->role;

        if ($user->save()) {
            $resp['status'] = true;
            $resp['message'] = "{$user->role} Registered!!";
            return response()->json($resp,200);
        } else {
            $resp['status'] = false;
            $resp['message'] = "Already Exsist";
            return response()->json($resp,400);
        }
    }
    public function sendOtp(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'mobile' => 'required|digits:10',          
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 400);
        }

            $otp = rand(100000, 999999);

        $user = User::updateOrCreate(
            [
                'mobile' => $request->mobile,
            ],

        );
        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(5);
        $user->save();

        return response()->json(['message' => 'OTP sent successfully','otp'=>$otp,'status'=>true],200);
        }
    

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10',
            'otp' => 'required',
        ]);

        $user = User::where('mobile', $request->mobile)
            ->where('otp', $request->otp)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid OTP','status'=>false], 401);
        }

        $user->otp = null;
        $user->save();

        $token = $user->createToken('API Token', [$user->role])->plainTextToken;

        return response()->json([
            'status'=>true,
            'message' => 'OTP verified',
            'token' => $token,
            'user' => $user
        ]);
    }

    private function sendOtpViaSms($mobile, $otp)
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.auth_token');
        $from = config('services.twilio.phone_number');

        $client = new Client($sid, $token);

        $client->messages->create($mobile, [
            'from' => $from,
            'body' => "Your OTP code is: $otp"
        ]);
    }

    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|digits:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(),'status'=>false], 422);
        }

        $user = User::where('mobile', $request->mobile)->first();

        if (!$user) {
            return response()->json(['error' => 'Mobile number not registered.','status'=>false], 404);
        }

        if ($user->otp_expires_at && Carbon::now()->diffInSeconds($user->otp_expires_at, false) > 240) {
            return response()->json(['error' => 'Please wait before requesting a new OTP.','status'=>false], 429);
        }

        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(5);

        if($user->save())
        {
            \Log::info("Resent OTP for {$user->mobile}: {$otp}");

            return response()->json(['message' => 'OTP resent successfully','status'=>true]);
        }
        else{
            return response()->json(['message' => 'Failed to send OTP','status'=>false], 500);
        }
        // ✅ Send OTP via Twilio
        // try {
        //     $twilio = new Client(
        //         config('services.twilio.sid'),
        //         config('services.twilio.token')
        //     );

        //     $twilio->messages->create(
        //         '+91' . $user->mobile, // Adjust for international format if needed
        //         [
        //             'from' => config('services.twilio.from'),
        //             'body' => "Your OTP is: {$otp}"
        //         ]
        //     );

        // } catch (\Exception $e) {
           
        // }      
    }
    public function index()
    {
       return view('auth.login-page');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Attempt login
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // $user = Auth::user();
            // if ($user) {
                return redirect()->route('/dashboard')->with('success', 'Admin logged in successfully');
            // } 
            // else
            // {
            //     return redirect()->back()->with('error', 'Invalid credentials');
            // }
        }
         else {
            // Authentication failed
            return redirect()->back()->with('error', 'Invalid credentials');
        }          
    
    }
}
