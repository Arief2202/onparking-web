<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Order;
use App\Models\MallList;
use Carbon\Carbon;

class LoginRegisterController extends Controller
{
    public function handleLogin(Request $request){        
        foreach(Order::all() as $order){            
            $now = Carbon::parse(date('Y-m-d H:i:s'));
            $expired = Carbon::parse($order->expired_time);
            if($now > $expired && $order->status == 0){
                $order->status = 4;
                $order->save();
            }
        }

        if(User::where('email', '=', $request->email)->first()){
            if(Auth::attempt(['email' => $request->email,'password' => $request->password])){
                $data['user'] = User::findOrFail(Auth::user()->id); 
                return response()->json(
                    [
                        'success' => true,
                        'data' => $data,
                        'pesan' => 'Login Successfully'
                    ],
                    200
                )->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            }
            else{
                return response()->json(
                    [
                        'success' => false,
                        'data' => '',
                        'pesan' => 'Wrong Email or Password !'
                    ],
                    400
                )->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            }
        }
        else{
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'pesan' => 'User not Registered !'
                ],
                400
            )->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        }
    }
    public function handleLoginEncrypted(Request $request){

        foreach(Order::all() as $order){            
            $now = Carbon::parse(date('Y-m-d H:i:s'));
            $expired = Carbon::parse($order->expired_time);
            if($now > $expired && $order->status == 0){
                $order->status = 4;
                $order->save();
            }
        }
        if($user = User::where('email', '=', $request->email)->first()){
            if($user->email == $request->email && $user->password == $request->password){
                $data['user'] = $user; 
                return response()->json(
                    [
                        'success' => true,
                        'data' => $data,
                        'pesan' => 'Login Successfully'
                    ],
                    200
                )->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            }
            else{
                return response()->json(
                    [
                        'success' => false,
                        'data' => '',
                        'pesan' => 'Wrong Email or Password !'
                    ],
                    400
                )->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            }
        }
        else{
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'pesan' => 'Wrong Email or Password !'
                ],
                400
            )->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        }
    }
    public function handleRegister(Request $request){        
        foreach(Order::all() as $order){            
            $now = Carbon::parse(date('Y-m-d H:i:s'));
            $expired = Carbon::parse($order->expired_time);
            if($now > $expired && $order->status == 0){
                $order->status = 4;
                $order->save();
            }
        }
        if(strlen($request->name) < 1) return response()->json(['success' => false,'data' => '','pesan' => 'Name Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(strlen($request->email) < 1) return response()->json(['success' => false,'data' => '','pesan' => 'Email Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(strlen($request->password) < 1) return response()->json(['success' => false,'data' => '','pesan' => 'Password Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(strlen($request->card_id) < 1) return response()->json(['success' => false,'data' => '','pesan' => 'ID Card Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(User::where('email', '=', $request->email)->first()) return response()->json(['success' => false,'data' => '','pesan' => 'Email has been Registered!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');

        User::insert([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'card_id' => $request->card_id
        ]);
        
        // $data['user'] = User::findOrFail(Auth::user()->id); 
        $data['user'] = User::where('email', '=', $request->email)->first();
        return response()->json(
            [
                'success' => true,
                'data' => $data,
                'pesan' => 'Login Successfully'
            ],
            200
        )->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
}
