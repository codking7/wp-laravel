<!DOCTYPE html>
<html lang="en">
<head>
    @include('spark::layouts.common.head')
</head>
<body class="with-top-navbar">
    <!-- Vue App For Spark Screens -->
    <div @if(isSparkView()) v-cloak id="spark-app"@endif>
        <div class="growl" id="app-growl"></div>

        <!-- Navigation -->
        @if (Auth::check())
            @include('spark::nav.authenticated')
        @else
            @include('spark::nav.guest')
        @endif

        @yield('content')

        <!-- Footer -->
        @include('spark::common.footer')

        @include('common/footer')
    </div>

    <div id="cmv-spinner"><span></span></div>
</body>
</html>
