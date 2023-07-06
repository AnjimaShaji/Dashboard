@extends('layouts.error')

@section('content')


    <div class="page-error centered"> 
        <div class="error-symbol"> 
            <i class="fa-warning"></i> 
        </div> 
        <h2> Error 403 <small>Forbidden!</small> </h2>
        <p>You don't have permission to access / on this server!</p> 
        <p>You might not have the necessary permissions for this resource!</p> 
    </div>

    <div class="page-error-search centered">
        
        <a href="{{ route('login') }}" class="go-back"> <i class="fa-angle-left"></i> Go Back </a> 
    </div>

@endsection