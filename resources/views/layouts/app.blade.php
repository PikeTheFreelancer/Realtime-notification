<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>

<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                            <li class="nav-item dropdown dropdown-notifications">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle notification-box" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Notification<span class="caret"></span>
                                </a>
                                
                                <div class="dropdown-menu dropdown-menu-right menu-notification" aria-labelledby="navbarDropdown">
                                    @foreach ($notifications as $notification)
                                        @php
                                            $data = json_decode($notification->data);
                                        @endphp
                                        <a class="dropdown-item noti-item @if(!$notification->read_at) noti-unread @endif" data-id={{$notification->id}} href="#">
                                            <span>{{ $data->noti_from }}</span><br>
                                            <small>{{ $data->content }}</small>
                                        </a>
                                    @endforeach
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="https://js.pusher.com/4.4/pusher.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <script type="text/javascript">
        var pusher = new Pusher('{{ Config::get('broadcasting.connections.pusher.key') }}', {
            encrypted: true,
            cluster: "ap1"
        });
        @if (Auth::check()) 
            var recipant = {{ Auth::user()->id }};
            var channel = pusher.subscribe('NotificationEvent');
            channel.bind(recipant, function(data) {
                var newNotificationHtml = `
                <a class="dropdown-item noti-item noti-unread" href="#" data-id=${data.id}>
                    <span>${data.noti_from}</span><br>
                    <small>${data.content}</small>
                </a>
                `;
                var newNotilabel = "<span class='new-notification'>!</span>";
                $('.menu-notification').prepend(newNotificationHtml);
                $('.notification-box').prepend(newNotilabel);
            });
        @endif
        $(document).on('click', '.notification-box', function() {
            $('.new-notification').addClass('hidden');
        })
        $(document).on('click', '.noti-item', function(e) {
            e.preventDefault();
            var noti_id = $(this).data('id');
            console.log(noti_id);
            var this_noti = $(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: 'notification/mark-as-read',
                method: 'POST',
                data: { noti_id: noti_id }, // Send the ID as data
                success: function(response) {
                    // Handle the success response from the controller
                    console.log('Marked as read:', response);
                    this_noti.removeClass('noti-unread');
                },
                error: function(error) {
                    // Handle any errors that occur during the Ajax request
                    console.error('Error:', error);
                }
            });
        })
    </script>
</body>
</html>
