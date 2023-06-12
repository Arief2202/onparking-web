<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Order;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function webUserList(Request $request)
    {
        if(Auth::user()->role != 1) return redirect('/profile');
        return view('userList', [
            'users' => User::where('email', '!=', 'admin')->get()
        ]);
    }

    public function edit(Request $request)
    {
        return redirect('/profile/'.Auth::user()->id);
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }
    public function edit2($id, Request $request)
    {
        if(!User::where('id', $id)->first()) return redirect('/user/list');
        if(Auth::user()->role != 1 && $id != Auth::user()->id) return redirect('/profile/'.Auth::user()->id);
        $user = User::where('id', $id)->first();
        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = User::where('id', $request->id)->first();
        if(User::where('email', $request->email)->where('id', '!=', $request->id)->first()) return redirect('/profile/'.$request->id)->with('error', 'email');
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect('/profile/'.$request->id)->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        if(Auth::user()->role == 0){
            $request->validateWithBag('userDeletion', [
                'password' => ['required', 'current-password'],
            ]);
        }

        $user = User::where('id', $request->id)->first();
        foreach(Order::where('user_id', $user->id)->get() as $order){
            $order->delete();
        }
        
        if(Auth::user()->role == 0) Auth::logout();

        $user->delete();

        if(Auth::user()->role == 0) $request->session()->invalidate();
        if(Auth::user()->role == 0) $request->session()->regenerateToken();
        if(Auth::user()->role == 0) return Redirect::to('/');
        else return Redirect::to('/user/list');
    }
}
