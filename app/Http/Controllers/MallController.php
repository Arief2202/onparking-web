<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Order;
use App\Models\spot_parkir;
use App\Models\MallList;
use Carbon\Carbon;

class MallController extends Controller
{
    public function updateMall(Request $request){
        $mall = MallList::where('id', $request->id)->first();
        if($mall->user_id != Auth::user()->id) return redirect('/mymall');
        if(isset($request->fotoMall)) $mall->fotoMall = $request->fotoMall;
        if(isset($request->namaMall)) $mall->namaMall = $request->namaMall;
        if(isset($request->alamatMall)) $mall->alamatMall = $request->alamatMall;
        if(isset($request->openTimeMall)) $mall->openTimeMall = $request->openTimeMall;
        $mall->save();
        return redirect('/mymall')->with('status', 'mall-updated');
    }

    public function webMyMallList(){
        $mall = MallList::where('user_id', Auth::user()->id)->first();
        if(!$mall) return redirect('/profile');
        return view('myMall', [
            'mall' => $mall,
            'spots' => spot_parkir::where('mall_id', $mall->id)->get(),
        ]);
    }

    public function tambahMall(Request $request){
        $mall = new MallList();
        $mall->namaMall = $request->namaMall;
        $mall->alamatMall = "-";
        $mall->openTimeMall = "-";
        $mall->fotoMall = "/no_image.png";
        $mall->save();
        return redirect('/mall/list');
    }
    public function deleteMall(Request $request){
        $mall = MallList::where('id', $request->mall_id)->first();
        foreach(spot_parkir::where('mall_id', $mall->id)->get() as $spot){            
            foreach(Order::where('spot_parkir_id', $spot->id)->get() as $order) $order->delete();
            $spot->delete();
        }
        $mall->delete();
        return redirect('/mall/list');
    }
    public function changeOwner(Request $request){
        $mall = MallList::where('id', $request->mall_id)->first();
        if(MallList::where('id', '!=', $request->mall_id)->where('user_id', $request->user_id)->first() && $request->user_id != null) return redirect('/mall/list');
        $mall->user_id = $request->user_id;
        $mall->save();
        return redirect('/mall/list');
    }
    
    public function webMallList(){
        if(Auth::user()->role != 1) return redirect('/profile');
        return view('mallList', [
            'malls' => MallList::all(),
        ]);
    }

    public function test(){
                return response()->json(
            [
                'success' => true,
                'mall' => MallList::where('id', '=', 1)->first(),
                'data_parkir' => $myArray,
                'pesan' => ''
            ],
            200
        )->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
    public function readMall(){        
        foreach(Order::all() as $order){            
            $now = Carbon::parse(date('Y-m-d H:i:s'));
            $expired = Carbon::parse($order->expired_time);
            if($now > $expired && $order->status == 0){
                $order->status = 4;
                $order->save();
            }
        }

        $malls = [];
        $index = 0;
        foreach(MallList::all() as $mall){
            $spotParkirs = spot_parkir::where('mall_id', '=', $mall->id)->get();
            $orderCount = 0;
            $key = 0;
            $spot = [];
            $order = Order::whereIn('spot_parkir_id', $spotParkirs->pluck('id')->toArray())->where('status', '<', '2')->get();
            // dd($order->pluck('spot_parkir_id'))
            $orderCount = $order->count();
            // dd($spotParkirs);
            // dd($spotParkirs->whereNotIn('id', $order->pluck('spot_parkir_id')->toArray())->pluck('lantai'));
            foreach($spotParkirs->whereNotIn('id', $order->pluck('spot_parkir_id'))->pluck('lantai')->unique()->toArray() as $lantai){
                if(spot_parkir::where('mall_id', '=', $mall->id)->whereNotIn('id', $order->pluck('spot_parkir_id'))->where('lantai', '=', $lantai)->where('carExist', '=', 0)->pluck('blok')->count() != 0){
                    $spot[$key++] = ([
                        'lantai' => (String) $lantai,
                        'blok'=>spot_parkir::where('mall_id', '=', $mall->id)->whereNotIn('id', $order->pluck('spot_parkir_id'))->where('lantai', '=', $lantai)->where('carExist', '=', 0)->pluck('blok')->toArray(),
                    ]);
                }
            }
            // dd($order->pluck('spot_parkir_id')->toArray());
            $orderCount += $spotParkirs->whereNotIn('id', $order->pluck('spot_parkir_id'))->where('carExist', '=', 1)->count();
            $malls[$index]['id'] = $mall->id;
            $malls[$index]['fotoMall'] = $mall->fotoMall;
            $malls[$index]['namaMall'] = $mall->namaMall;
            $malls[$index]['alamatMall'] = $mall->alamatMall;
            $malls[$index]['openTimeMall'] = $mall->openTimeMall;
            $malls[$index]['kuotaMall'] = $spotParkirs->count();
            $malls[$index]['orderCount'] = $orderCount;
            $malls[$index]['spot_ready'] = $spot;
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
            $now = Carbon::parse(date('Y-m-d H:i:s'));
            $expired = Carbon::parse($order->expired_time);
            if($now > $expired && $order->status == 0){
                $order->status = 4;
                $order->save();
            }
        }

        return response()->json(
            [
                'success' => true,
                'mall' => MallList::where('id', '=', $id)->get(),
                'booking' => Order::whereIn('spot_parkir_id', spot_parkir::where('mall_id', '=', $id)->pluck('id')->toArray())->get(),
                'pesan' => ''
            ],
            200
        )->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
    public function readMallBooked($id){        
        foreach(Order::all() as $order){            
            $now = Carbon::parse(date('Y-m-d H:i:s'));
            $expired = Carbon::parse($order->expired_time);
            if($now > $expired && $order->status == 0){
                $order->status = 4;
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

    // public function deleteMall(Request $request){    
    //     if(strlen($request->email) < 1) return response()->json(['success' => false,'pesan' => 'Email Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    //     if(strlen($request->password) < 1) return response()->json(['success' => false,'pesan' => 'Password Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    //     if($user = User::where('email', '=', $request->email)->first()){
    //         if($user->email == $request->email && $user->password == $request->password){
    //             if($user->role != "1"){
    //                 return response()->json(
    //                     [
    //                         'success' => false,
    //                         'pesan' => 'You are not Admin!'
    //                     ],
    //                     400
    //                 )->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    //             }
    //         }
    //         else{
    //             return response()->json(
    //                 [
    //                     'success' => false,
    //                     'data' => '',
    //                     'pesan' => 'Wrong Email or Password !'
    //                 ],
    //                 400
    //             )->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    //         }
    //     }
    //     if(strlen($request->idMall) < 1) return response()->json(['success' => false,'pesan' => 'ID Mall Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    //     if(MallList::where('id', '=', $request->idMall)->first() == null) return response()->json(['success' => false,'pesan' => 'Mall not Found!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    //     MallList::where('id', '=', $request->idMall)->first()->delete();
    //     foreach(Order::where('mall_id', '=', $request->idMall)->get() as $order){
    //         $order->delete();
    //     }
    //     return response()->json(
    //         [
    //             'success' => true,
    //             'pesan' => 'Delete Successfully'
    //         ],
    //         200
    //     )->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    // }
}
