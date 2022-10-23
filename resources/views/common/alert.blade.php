@if(Session::has('message'))
    <div class="alert alert-info" role="alert">
        <div class="text-muted">{{Session('message')}}</div>
    </div>
@elseif(Session::has('success'))
    <div class="alert alert-success" role="alert">
        <div class="text-muted">{{Session('success')}}</div>
    </div>
@elseif(Session::has('error'))
    <div class="alert alert-danger" role="alert">
        <div class="text-muted">{{Session('error')}}</div>
    </div>
@endif
