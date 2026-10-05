@include('layouts.header')
@include('layouts.navbar')

<main>
    @yield('content')
</main>

@include('layouts.footer')
@include('layouts.whatsapp-widget')
@include('layouts.notification-popup')
@include('layouts.cookie-consent')
@include('layouts.scripts')