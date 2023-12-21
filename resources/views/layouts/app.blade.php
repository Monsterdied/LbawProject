<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('pagename')</title>

        <!-- Styles -->
        @yield('style')
        <link href="{{ url('css/errors.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link href="{{ url('css/milligram.min.css') }}" rel="stylesheet">
        <link href="{{ url('css/app.css') }}" rel="stylesheet">
        <link rel="canonical" href="https://getbootstrap.com/docs/5.2/examples/footers/">
        <link href="{{asset('images/webicon.ico')}}" rel = "icon">

        @yield('og')
                <script type="text/javascript">
                    
            // Fix for Firefox autofocus CSS bug
            // See: http://stackoverflow.com/questions/18943276/html-5-autofocus-messes-up-css-loading/18945951#18945951
        </script>
        <script type="text/javascript" src={{ url('js/app.js') }} defer>
            
        </script>
        <script type="text/javascript" src={{ url('js/notification.js') }} defer>
        

        </script>
    </head>
    <body>
        <main>
            <header>
                <h1><a href="{{ url('/home') }}">QthenA</a></h1>
                @if (Auth::check())
                    @php
                        $user = Auth::user();
                    @endphp
                    <div style="display:flex;align-items: center;" class="headernotifications">
                        <a href="{{ route('notifications_page')}}" style="display: flex; width: 3em; justify-content: flex-start;"class="notification">
                        <span class="material-symbols-outlined" style="color:white; margin:0em;">
                            notifications
                        </span>
                            <span class="badge" id="notification_numbers"></span>
                        </a>
                        <a id="profile" class="button" href="{{ route('profile', ['id' => $user->id]) }}">
                            <img style=" max-width: none; margin:0em;" src="{{ $user->getProfileImage() }}" alt="Profile Picture">
                        </a>
                    </div>
                @endif
            </header>
            <section id="content">
                <div id="errorNotifications" class="error">
                </div>
                @yield('content')
            </section>

            <div class="botoooom">
                <footer class="py-3 my-4">
                    <ul class="nav justify-content-center border-bottom pb-3 mb-3">
                        <li class="nav-item"><a href="/home" class="nav-link px-2 text-muted">Home</a></li>
                        <li class="nav-item"><a href="/faq" class="nav-link px-2 text-muted">FAQs</a></li>
                        <li class="nav-item"><a href="/about" class="nav-link px-2 text-muted">Contacts</a></li>
                        <li class="nav-item"><a href="/about" class="nav-link px-2 text-muted">About Us</a></li>
                    </ul>
                    <p class="text-center text-muted">© 2023 QthenA, In</p>
                </footer>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
        </main>
    </body>
</html>