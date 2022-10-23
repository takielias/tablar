@if(Session::has('message'))
    <div class="alert alert-info" role="alert">
        <h4 class="alert-title">Did you know?</h4>
        <div class="text-muted">Here is something that you might like to know.</div>
    </div>
@elseif(Session::has('success'))
    <div class="alert alert-success" role="alert">
        <h4 class="alert-title">Wow! Everything worked!</h4>
        <div class="text-muted">Your account has been saved!</div>
    </div>
@elseif(Session::has('error'))
    <div class="alert alert-danger" role="alert">
        <h4 class="alert-title">I'm so sorry&hellip;</h4>
        <div class="text-muted">Your account has been deleted and can't be restored.</div>
    </div>
@endif

@if ($errors->any())
    @foreach ($errors->all() as $error)
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-title">Error !</h4>
            <div class="text-muted">{{$error}}</div>
        </div>
    @endforeach
@endif
