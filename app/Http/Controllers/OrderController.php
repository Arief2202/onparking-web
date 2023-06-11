<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Order;
use App\Models\spot_parkir;
use App\Models\MallList;

class OrderController extends Controller
{
    public function handleOrder(Request $request){
        if(strlen($request->mall_id) < 1) return response()->json(['success' => false,'pesan' => 'Mall ID Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(strlen($request->user_id) < 1) return response()->json(['success' => false,'pesan' => 'User ID Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(strlen($request->lantai) < 1) return response()->json(['success' => false,'pesan' => 'Lantai Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(strlen($request->blok) < 1) return response()->json(['success' => false,'pesan' => 'Blok Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $spot = spot_parkir::where('mall_id', '=', $request->mall_id)->where('lantai', '=', $request->lantai)->where('blok', '=', $request->blok)->first();
        if(strlen($spot)<=2)return response()->json(['success' => false,'pesan' => 'Spot Parkir did\'nt Found!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        foreach(Order::where('user_id', '=', $request->user_id)->where('status', '<', '2')->get() as $order){
            if(spot_parkir::where('id', '=', $order->spot_parkir_id)->first()->mall_id == $request->mall_id) return response()->json(['success' => false,'pesan' => 'This user has been booking on this mall!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        }
        if(strlen(User::where('id', '=', $request->user_id)->get()) <= 2) return response()->json(['success' => false,'pesan' => 'User not Available!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(strlen(MallList::where('id', '=', $request->mall_id)->get()) <= 2) return response()->json(['success' => false,'pesan' => 'Mall not Available!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        // if(Order::where('spot_parkir_id', '=', $spot->id)->where('status', '<', '2')->where('user_id', '=', $request->user_id)->first()) return response()->json(['success' => false,'pesan' => 'You has Booked this Spot!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        // if(Order::where('spot_parkir_id', '=', $spot->id)->where('status', '<', '2')->first()) return response()->json(['success' => false,'pesan' => 'This Spot has Booked by another user!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        // dd(Order::where('spot_parkir_id', '=', $spot->id)->where('status', '<', '2')->first());

        

        $status = Order::insert([
            'user_id' => $request->user_id,
            'spot_parkir_id' => $spot->id,
            'order_time' => Carbon::parse(date('Y-m-d H:i:s')),
            'expired_time' => date(Carbon::now()->addMinutes(1)),
            'created_at' => Carbon::parse(date('Y-m-d H:i:s')),
        ]);
        $data['booking'] = Order::where('spot_parkir_id', '=', $spot->id)->where('user_id', '=', $request->user_id)->first();
        $data['user'] = User::where('id', '=', $request->user_id)->get();
        $data['mall'] = MallList::where('id', '=', $spot->mall_id)->get();
        
        return response()->json(
            [
                'success' => true,
                'data' => $data,
                'pesan' => 'Booking Successfully'
            ],
            200
        )->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        
    }
    public function readOrder($id){
        $orders = [];
        $index=0;
        
        foreach(Order::all() as $order){            
            $now = Carbon::parse(date('Y-m-d H:i:s'));
            $expired = Carbon::parse($order->expired_time);
            if($now > $expired && $order->status == 0){
                $order->status = 4;
                $order->save();
            }
        }

        foreach(Order::where('user_id', '=', $id)->get()->reverse() as $order){            
            $now = Carbon::parse(date('Y-m-d H:i:s'));
            $expired = Carbon::parse($order->expired_time);
            if($now > $expired && $order->status == 0){
                $order->status = 4;
                $order->save();
            }

            $spot_parkir = spot_parkir::where('id', '=', $order->spot_parkir_id)->first();
            $spotParkirs = spot_parkir::where('mall_id', '=', $spot_parkir->mall_id)->get();
            $orderCount = 0;
            $key = 0;
            $spot = [];
            foreach($spotParkirs->pluck('lantai')->unique()->toArray() as $lantai){
                $spot[$key++] = ([
                    'lantai' => (String) $lantai,
                    'blok'=>spot_parkir::where('mall_id', '=', $spot_parkir->mall_id)->where('lantai', '=', $lantai)->pluck('blok')->toArray(),
                ]);
            }
            $mall = MallList::where('id', '=', $spot_parkir->mall_id)->first();
            $malls['id'] = $spot_parkir->mall_id;
            $malls['fotoMall'] = $mall->fotoMall;
            $malls['namaMall'] = $mall->namaMall;
            $malls['alamatMall'] = $mall->alamatMall;
            $malls['openTimeMall'] = $mall->openTimeMall;
            $malls['kuotaMall'] = $spotParkirs->count();
            $malls['orderCount'] = Order::whereIn('spot_parkir_id', $spotParkirs->pluck('id')->toArray())->where('status', '<', '3')->count();
            $malls['spot_ready'] = $spot;

            $checkinTime =  new Carbon($order->checkin_time);
            if($order->checkout_time != null) $checkoutTime = new Carbon($order->checkout_time);
            else $checkoutTime = Carbon::parse(date('Y-m-d H:i:s'));

            $lamaParkir = $checkinTime->diff($checkoutTime);

            $hariParkir = (int) $lamaParkir->format('%d');
            $addHour = $hariParkir > 0 ? (int) $hariParkir*24 : 0;
            $jamParkir = (int) $lamaParkir->format('%H') + $addHour;

            $estimasi_harga = ((int) $jamParkir) * (int) $spot_parkir->harga;
            $spot_parkir->harga = number_format($spot_parkir->harga, 0, '','.');

            $orders[$index]['id'] = $order->id;
            $orders[$index]['spot_parkir'] = $spot_parkir;
            $orders[$index]['mall'] = $malls;
            $orders[$index]['progress'] = $order->status;
            $orders[$index]['expired_time'] = $order->expired_time == null ? null : date('H:i:s d M Y', strtotime($order->expired_time));
            $orders[$index]['order_time'] = $order->order_time == null ? null : date('H:i:s d M Y', strtotime($order->order_time));
            $orders[$index]['checkin_time'] = $order->checkin_time == null ? null : date('H:i:s d M Y', strtotime($order->checkin_time));
            $orders[$index]['checkout_time'] = $order->checkout_time == null ? null : date('H:i:s d M Y', strtotime($order->checkout_time));
            $orders[$index]['durasi_parkir'] =  $jamParkir.$lamaParkir->format(' Jam, %i Menit');
            $orders[$index]['estimasi_harga'] = number_format($estimasi_harga, 0, '','.');
            $index++;
        }
        $user = User::where('id', '=', $id)->first();
        return response()->json(
            [                
                'success' => $user ? true : false,
                'user' => $user,
                'totalOrders' => Order::where('user_id', '=', $id)->get()->count(),
                'orders' => $orders,
                'pesan' => $user ? '' : 'User not found!'
            ],
            $user? 200 : 400
        )->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        
    }
    
    public function cancelOrder(Request $request){
        if(strlen($request->order_id) < 1) return response()->json(['success' => false,'pesan' => 'Order ID Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');        
        if(strlen($request->user_id) < 1) return response()->json(['success' => false,'pesan' => 'User ID Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(strlen(User::where('id', '=', $request->user_id)->get()) <= 2) return response()->json(['success' => false,'pesan' => 'User not found!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(strlen($request->user_password) < 1) return response()->json(['success' => false,'pesan' => 'User Password Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(User::where('id', '=', $request->user_id)->first()->password !=  $request->user_password)  return response()->json(['success' => false,'pesan' => 'Wrong Password!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(strlen(Order::where('id', '=', $request->order_id)->get()) <= 2) return response()->json(['success' => false,'pesan' => 'Order not found!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(Order::where('id', '=', $request->order_id)->first()->user_id != $request->user_id) return response()->json(['success' => false,'pesan' => 'This user not booking on this order!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        // if(strlen(User::where('id', '=', $request->user_id)->get()) <= 2) return response()->json(['success' => false,'pesan' => 'User not Available!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        // if(strlen(MallList::where('id', '=', $request->mall_id)->get()) <= 2) return response()->json(['success' => false,'pesan' => 'Mall not Available!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        // $user = null;
        $order = Order::findOrFail($request->order_id);
        $order->status = 3;
        $order->save();
        return response()->json(
            [                
                'success' => true,
                'pesan' => 'Cancel Order Successfully'
            ],
            200
        )->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        
    }
    
    public function handleCheckin(Request $request){
        if(strlen($request->spot_id) < 1) return response()->json(['success' => false,'pesan' => 'Spot ID Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');        
        if(strlen($request->card_id) < 1) return response()->json(['success' => false,'pesan' => 'Card ID Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(strlen(User::where('card_id', '=', $request->card_id)->get()) <= 2) return response()->json(['success' => false,'pesan' => 'User with this card not found!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $spot = spot_parkir::where('id', '=', $request->spot_id)->first();
        if(!$spot)return response()->json(['success' => false,'pesan' => 'Parking Spot Did\'nt Found!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $user = User::where('card_id', '=', $request->card_id)->first();
        $orders = Order::where('spot_parkir_id', '=', $spot->id)->where('user_id', '=', $user->id)->get();  
        if(strlen($orders->where('status', '=', '0')) <= 2) return response()->json(['success' => false,'pesan' => 'This User Doesn\'t Have Waiting Status on this spot!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $order = $orders->where('status', '=', '0')->first();
        $order->status = 1;
        $order->checkIn_time = Carbon::parse(date('Y-m-d H:i:s'));        
        $order->save();
        
        return response()->json(
            [                
                'success' => true,
                'pesan' => 'Check-In Successfully'
            ],
            200
        )->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }

    public function handleCheckout(Request $request){
        if(strlen($request->spot_id) < 1) return response()->json(['success' => false,'pesan' => 'Spot ID Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');        
        // if(strlen($request->card_id) < 1) return response()->json(['success' => false,'pesan' => 'Card ID Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        // if(strlen(User::where('card_id', '=', $request->card_id)->get()) <= 2) return response()->json(['success' => false,'pesan' => 'User with this card not found!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $spot = spot_parkir::where('id', '=', $request->spot_id)->first();
        if(!$spot)return response()->json(['success' => false,'pesan' => 'Parking Spot Did\'nt Found!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        // $user = User::where('card_id', '=', $request->card_id)->first();
        $orders = Order::where('spot_parkir_id', '=', $spot->id)->where('status', '=', "1")->get();  

        // if(strlen($orders->where('status', '=', '1')) <= 2) return response()->json(['success' => false,'pesan' => 'This User Doesn\'t Have Check-in Status on this mall!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        foreach($orders as $order){
            // $order = $orders->where('status', '=', '1')->first();
            $order->status = 2;
            $order->checkout_time = Carbon::parse(date('Y-m-d H:i:s'));        
            $order->save();
        }
        return response()->json(
            [                
                'success' => true,
                'pesan' => 'Check-Out Successfully'
            ],
            200
        )->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }

    
    public function checkCard(Request $request)
    {
        if(strlen($request->spot_parkir_id) < 1) return response()->json(['success' => false,'pesan' => 'Spot Parkir Id Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(strlen($request->state) < 1) return response()->json(['success' => false,'pesan' => 'State Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if($request->state < 0 || $request->state > 1) return response()->json(['success' => false,'pesan' => 'Please input only 0 or 1 on state!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $spot = spot_parkir::where('id', '=', $request->spot_parkir_id)->first();
        $spot->carExist = $request->state;
        $spot->save();        
        return response()->json([
            'success' => true,
            'data' => $spot,
            'pesan' => ''
        ],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
}
