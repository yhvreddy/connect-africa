@php
    $guard = app('truFlix')->getCurrentGuard();
@endphp
<div class="sidebar-wrapper" sidebar-layout="stroke-svg">
    <div>
        <div class="logo-wrapper">
            <a href="{{route('/')}}">
                <img class="img-fluid for-light" src="{{ asset('assets/src/images/log.png') }}" alt="">
                <img class="img-fluid for-dark" src="{{ asset('assets/src/images/log.png') }}" alt="">
            </a>
            <div class="back-btn"><i class="fa fa-angle-left"></i></div>
            <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="grid"> </i></div>
        </div>
        <div class="logo-icon-wrapper">
            <a href="{{route('/')}}"><img class="img-fluid" src="{{ asset('assets/src/images/TF-logo.png') }}" alt=""></a></div>
        @if ($guard == 'zq')
            @include('layouts.simple.menus.zq')
        @elseif ($guard == 'admin')
            @include('layouts.simple.menus.admin')
        @elseif ($guard == 'partner')
            @include('layouts.simple.menus.partner')
        @endif
    </div>
</div>
