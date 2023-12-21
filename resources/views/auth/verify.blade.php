@extends('layouts.app')
@section ('style')
  <link href="{{ url('css/auth.css') }}" rel="stylesheet">
@endsection
@section('content')
<div style ="padding:1em;">
<h2 style= "display: flex;
    justify-content: space-evenly;">
    Hello {{ $user->name }}
</h2>
<p style= "display: flex;
    justify-content: space-evenly;">
    You have requested to reset your password. Please click the link below to reset your password.
</p>

  <a sytle="display: flex;
    color: white !important;
    width: 20em;
    justify-content: space-evenly;
    align-items: center;" class="button button-primary" href="{{ route('reset', ['token' => $user->remember_token]) }}">Reset Password</a>
</div>

@endsection