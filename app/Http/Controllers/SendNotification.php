<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Notifications\TestNotification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification as FacadesNotification;
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
        
        $data = $request->only([
            'title',
            'content',
            'noti_to',
            'noti_from',
        ]);
        
        $user->notify(new TestNotification($data));

        $options = array(
            'cluster' => 'ap1',
            'encrypted' => true
        );

        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );

        $pusher->trigger('NotificationEvent', $request['noti_to'], $data);

        return redirect()->route('home');
    }
}