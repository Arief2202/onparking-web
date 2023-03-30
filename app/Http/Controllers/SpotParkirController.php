<?php

namespace App\Http\Controllers;

use App\Http\Requests\Storespot_parkirRequest;
use App\Http\Requests\Updatespot_parkirRequest;
use App\Models\spot_parkir;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;

class SpotParkirController extends Controller
{
    public function setCarExist(Request $request)
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
        ],200)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
    public function showSpotDetail($id)
    {
        $spot = spot_parkir::where('id', '=', $id)->first();
        if(strlen($spot) <= 2) return response()->json(['success' => false,'pesan' => 'Spot not Found!'],400)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $user_order = null;
        $order_status = Order::where('spot_parkir_id', '=', $spot->id)->where('status', '<', '2')->first();
        if($order_status) $user_order = User::where('id', '=', $order_status->user_id)->first();
        // dd($user_order);
        return response()->json([
            'success' => true,
            'data' => $spot,
            'ordered_by' => $user_order,
            'order_status' => $order_status,
            'pesan' => ''
        ],200)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
    
}
