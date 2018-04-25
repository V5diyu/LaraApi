<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class NotificationsController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // 获取登录用户的所有的通知
        $notifications = Auth::user()->notifications()->paginate(20);

        Auth::user()->markAsRead();
        //dd($notifications);
        return view('notifications.index',compact('notifications'));
    }


}
