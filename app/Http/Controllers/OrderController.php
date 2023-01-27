<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Order;
use App\Models\MallList;

class OrderController extends Controller
{
    public function handleOrder(Request $request){
        if(strlen($request->mall_id) < 1) return response()->json(['success' => false,'pesan' => 'Mall ID Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(strlen($request->user_id) < 1) return response()->json(['success' => false,'pesan' => 'User ID Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(Order::where('mall_id', '=', $request->mall_id)->where('user_id', '=', $request->user_id)->where('progress', '<', '2')->first()) return response()->json(['success' => false,'pesan' => 'This user has been booking on this mall!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(strlen(User::where('id', '=', $request->user_id)->get()) <= 2) return response()->json(['success' => false,'pesan' => 'User not Available!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(strlen(MallList::where('id', '=', $request->mall_id)->get()) <= 2) return response()->json(['success' => false,'pesan' => 'Mall not Available!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        
        $orderCount = Order::where('mall_id', '=', $request->mall_id)->where('progress', '<', '2')->get()->count();
        $mallInfo = MallList::findOrFail($request->mall_id);
        if($orderCount >= $mallInfo->kuotaMall) return response()->json(['success' => false,'pesan' => 'Mall is Full!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $status = Order::insert([
            'user_id' => $request->user_id,
            'mall_id' => $request->mall_id,
            'order_time' => date('Y-m-d H:i:s'),
            'expired_time' => date(Carbon::now()->addMinutes(1)),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $data['booking'] = Order::where('mall_id', '=', $request->mall_id)->where('user_id', '=', $request->user_id)->first();
        $data['user'] = User::where('id', '=', $request->user_id)->get();
        $data['mall'] = MallList::where('id', '=', $request->mall_id)->get();
        
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
            $now = Carbon::parse(date('Y-m-d H:i:s'))->addHour(7);
            $expired = Carbon::parse($order->expired_time)->addHour(7);
            if($now > $expired && $order->progress == 0){
                $order->progress = 4;
                $order->save();
            }
        }

        foreach(Order::where('user_id', '=', $id)->get()->reverse() as $order){            
            $now = Carbon::parse(date('Y-m-d H:i:s'))->addHour(7);
            $expired = Carbon::parse($order->expired_time)->addHour(7);
            if($now > $expired && $order->progress == 0){
                $order->progress = 4;
                $order->save();
            }
            $orders[$index]['id'] = $order->id;
            $orders[$index]['mall'] = MallList::where('id', '=', $order->mall_id)->first();
            $orders[$index]['progress'] = $order->progress;
            $orders[$index]['expired_time'] = $order->expired_time;
            $orders[$index]['order_time'] = $order->order_time;
            $orders[$index]['checkIn_time'] = $order->checkIn_time;
            $orders[$index]['checkOut_time'] = $order->checkOut_time;
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
        $order->progress = 3;
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
        if(strlen($request->id_mall) < 1) return response()->json(['success' => false,'pesan' => 'Mall ID Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');        
        if(strlen($request->card_id) < 1) return response()->json(['success' => false,'pesan' => 'Card ID Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(strlen(User::where('card_id', '=', $request->card_id)->get()) <= 2) return response()->json(['success' => false,'pesan' => 'User with this card not found!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(strlen(MallList::where('id', '=', $request->id_mall)->get()) <= 2) return response()->json(['success' => false,'pesan' => 'Mall not found!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $user = User::where('card_id', '=', $request->card_id)->first();
        $orders = Order::where('mall_id', '=', $request->id_mall)->where('user_id', '=', $user->id)->get();
        
        if(strlen($orders->where('progress', '=', '0')) <= 2) return response()->json(['success' => false,'pesan' => 'This User Doesn\'t Have Waiting Status on this mall!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        
        $order = $orders->where('progress', '=', '0')->first();
        $order->progress = 1;
        $order->checkIn_time = date('Y-m-d H:i:s');        
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
        if(strlen($request->id_mall) < 1) return response()->json(['success' => false,'pesan' => 'Mall ID Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');        
        if(strlen($request->card_id) < 1) return response()->json(['success' => false,'pesan' => 'Card ID Required!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(strlen(User::where('card_id', '=', $request->card_id)->get()) <= 2) return response()->json(['success' => false,'pesan' => 'User with this card not found!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        if(strlen(MallList::where('id', '=', $request->id_mall)->get()) <= 2) return response()->json(['success' => false,'pesan' => 'Mall not found!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $user = User::where('card_id', '=', $request->card_id)->first();
        $orders = Order::where('mall_id', '=', $request->id_mall)->where('user_id', '=', $user->id)->get();
        
        if(strlen($orders->where('progress', '=', '1')) <= 2) return response()->json(['success' => false,'pesan' => 'This User Doesn\'t Have Check-in Status on this mall!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $order = $orders->where('progress', '=', '1')->first();
        $order->progress = 2;
        $order->checkout_time = date('Y-m-d H:i:s');        
        $order->save();
        return response()->json(
            [                
                'success' => true,
                'pesan' => 'Check-Out Successfully'
            ],
            200
        )->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
}
