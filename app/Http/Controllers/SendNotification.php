<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\TestNotification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Pusher\Pusher;

class SendNotification extends Controller
{
    public function create()
    {
        return view('notification');
    }

    public function store(Request $request)
    {
        $user_id = Auth::user()->id;
        $user = User::find($user_id);
        $request['noti_from'] = $user->name;

        $recipant =  User::find($request['noti_to']);
        
        $data = $request->only([
            'title',
            'content',
            'noti_to',
            'noti_from',
        ]);

        // save recipant id to notifiable_id column
        $recipant->notify(new TestNotification($data));

        // pass notifiation id to view for unread function
        $notification_id = DB::table('notifications')->orderBy('created_at', 'desc')->first()->id;
        $data['id'] = $notification_id;

        $options = array(
            'cluster' => 'ap1',
            'encrypted' => true
        );

        // do not use env() other than config files, use Config::get() instead
        $pusher = new Pusher(
            Config::get('broadcasting.connections.pusher.key'),
            Config::get('broadcasting.connections.pusher.secret'),
            Config::get('broadcasting.connections.pusher.app_id'),
            $options
        );

        // $request['noti_to'] is the chanel which passed to script to sent to a specific user
        $pusher->trigger('NotificationEvent', $request['noti_to'], $data);

        return redirect()->route('home');
    }

    public function markAsRead(Request $request)
    {
        $noti_id = $request->input('noti_id');
        $notification = auth()->user()->unreadNotifications->where('id', $noti_id)->first();
        if($notification) {
            $notification->markAsRead();
            return response()->json(['message' => 'Notification marked as read']);
        }else{
            return response()->json(['error' => 'Notification not found'], 404);
        }
    }
}