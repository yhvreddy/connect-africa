@if(session('error'))
    <div class="alert alert-warning p-2 custom_alert" role="alert">{{ session('error') }}</div>
@endif

@if(session('success'))
    <div class="alert alert-success p-2 custom_alert" role="alert">{{ session('success') }}</div>
@endif

@if(session('danger'))
    <div class="alert alert-danger p-2 custom_alert" role="alert">{{ session('danger') }}</div>
@endif

@if(session('failed'))
    <div class="alert alert-danger p-2 custom_alert" role="alert">{{ session('failed') }}</div>
@endif

@if(session('info'))
    <div class="alert alert-info p-2 custom_alert" role="alert">{{ session('info') }}</div>
@endif
