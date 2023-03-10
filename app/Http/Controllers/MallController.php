<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Order;
use App\Models\MallList;
use Carbon\Carbon;

class MallController extends Controller
{
    public function readMall(){        
        foreach(Order::all() as $order){            
            $now = Carbon::parse(date('Y-m-d H:i:s'))->addHour(7);
            $expired = Carbon::parse($order->expired_time)->addHour(7);
            if($now > $expired && $order->progress == 0){
                $order->progress = 4;
                $order->save();
            }
        }

        $malls = [];
        $index = 0;
        foreach(MallList::all() as $mall){
            $malls[$index]['id'] = $mall->id;
            $malls[$index]['fotoMall'] = $mall->fotoMall;
            $malls[$index]['namaMall'] = $mall->namaMall;
            $malls[$index]['alamatMall'] = $mall->alamatMall;
            $malls[$index]['openTimeMall'] = $mall->openTimeMall;
            $malls[$index]['kuotaMall'] = $mall->kuotaMall;
            $malls[$index]['orderCount'] = Order::where('mall_id', '=', $mall->id)->where('progress', '<', '2')->get()->count();
            $index++;
        }
        return response()->json(
            [
                'success' => true,
                'totalResult' => MallList::all()->count(),
                'mall' => $malls,
                'pesan' => ''
            ],
            200
        )->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
    public function detailMall($id){        
        foreach(Order::all() as $order){            
            $now = Carbon::parse(date('Y-m-d H:i:s'))->addHour(7);
            $expired = Carbon::parse($order->expired_time)->addHour(7);
            if($now > $expired && $order->progress == 0){
                $order->progress = 4;
                $order->save();
            }
        }

        return response()->json(
            [
                'success' => true,
                'mall' => MallList::where('id', '=', $id)->get(),
                'booking' => MallList::where('id', '=', $id)->get(),
                'pesan' => ''
            ],
            200
        )->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
    public function readMallBooked($id){        
        foreach(Order::all() as $order){            
            $now = Carbon::parse(date('Y-m-d H:i:s'))->addHour(7);
            $expired = Carbon::parse($order->expired_time)->addHour(7);
            if($now > $expired && $order->progress == 0){
                $order->progress = 4;
                $order->save();
            }
        }

        return response()->json(
            [
                'success' => true,
                'totalResult' => Order::where('mall_id', '=', $id)->get()->count(),
                'mall' => MallList::where('id', '=', $id)->get(),
                'status' => Order::where('mall_id', '=', $id)->get(),
                'pesan' => ''
            ],
            200
        )->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
    
    public function createMall(Request $request){  
        if(strlen($request->email) < 1) return response()->json(['success' => false,'pesan' => '"Email" Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(strlen($request->password) < 1) return response()->json(['success' => false,'pesan' => '"Password" Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if($user = User::where('email', '=', $request->email)->first()){
            if($user->email == $request->email && $user->password == $request->password){
                if($user->role != "1"){
                    return response()->json(
                        [
                            'success' => false,
                            'pesan' => 'You are not Admin!'
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
        
        if(strlen($request->fotoMall) < 1) return response()->json(['success' => false,'pesan' => 'FotoMall Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(strlen($request->namaMall) < 1) return response()->json(['success' => false,'pesan' => 'namaMall Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(strlen($request->alamatMall) < 1) return response()->json(['success' => false,'pesan' => 'alamatMall Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(strlen($request->openTimeMall) < 1) return response()->json(['success' => false,'pesan' => 'openTimeMall Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(strlen($request->kuotaMall) < 1) return response()->json(['success' => false,'pesan' => 'kuotaMall Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        
        MallList::insert([
            'namaMall' => $request->namaMall, 'alamatMall' => $request->alamatMall, 'openTimeMall' => $request->openTimeMall, 'fotoMall' => $request->fotoMall, 'kuotaMall' => $request->kuotaMall
        ]);
        
        return response()->json(
            [
                'success' => true,
                'pesan' => 'Create Succesfully'
            ],
            200
        )->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }

    public function deleteMall(Request $request){    
        if(strlen($request->email) < 1) return response()->json(['success' => false,'pesan' => 'Email Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(strlen($request->password) < 1) return response()->json(['success' => false,'pesan' => 'Password Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if($user = User::where('email', '=', $request->email)->first()){
            if($user->email == $request->email && $user->password == $request->password){
                if($user->role != "1"){
                    return response()->json(
                        [
                            'success' => false,
                            'pesan' => 'You are not Admin!'
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
        if(strlen($request->idMall) < 1) return response()->json(['success' => false,'pesan' => 'ID Mall Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(MallList::where('id', '=', $request->idMall)->first() == null) return response()->json(['success' => false,'pesan' => 'Mall not Found!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        MallList::where('id', '=', $request->idMall)->first()->delete();
        foreach(Order::where('mall_id', '=', $request->idMall)->get() as $order){
            $order->delete();
        }
        return response()->json(
            [
                'success' => true,
                'pesan' => 'Delete Successfully'
            ],
            200
        )->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
}
