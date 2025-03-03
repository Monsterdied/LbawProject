@extends('layouts.app')

@section('style')
@endsection

@section('pagename')
Moderate Content
@endsection 

@section('og')
    <meta property="og:title" content="Moderate Content" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url('/moderatecontent') }}" />
    <meta property="og:description" content="Moderate Content" />
    <meta property="og:image" content="{{ asset('images/icon.png') }}" />
@endsection

@section('content')
<link href="{{ asset('css/moderatecontent.css') }}" rel="stylesheet">
<div class="sidebar">
  <a href="/home">Home Page</a>
  <a href="/feed">Feed</a>
  <a href="{{'/tags'}}">Tags</a>
  <a href="{{'/questions'}}">Questions</a>
  <a href="{{'/users'}}">Users</a>
  <a class="active" href="{{'/moderatecontent'}}">Blocked Content</a>

</div>

<div style="color:white; font-size:0.0001em;">Home</div>

<div id="buttonlist">
<form action="{{ route('moderatecontent')}}" method='get' >
    <button class="active"type='submit' class='edit' name="edit-button">View Content Unblock Requests</button>
</form>
<form action="{{ route('moderateusers') }}" method='get' >
    <button type='submit' class='edit' name="edit-button">View Account Unblock Requests</button>
</form>
</div>

<div id="fixable" class="card-container   unblockcontent">
  <p>Blocked Content Appeals</p>
  @if($errors->has('unblockrequest'))
      <span class="error">
          {{ $errors->first('unblockrequest') }}
      </span>
  @endif
  @if($errors->has('content'))
      <span class="error">
          {{ $errors->first('content') }}
      </span>
  @endif
  @foreach ($unblockRequests as $unblockRequest)
  <div class="card">
      <p class="card-info">Type:{{ $unblockRequest->type}}</p>
      <p class="card-info">Unblock Request ID:{{ $unblockRequest->id}}</p>
      <p class="card-info">Name:{{ $unblockRequest->user->name}}</p>
      <p class="card-info">UserName:{{ $unblockRequest->user->username}}</p>
      <p class="card-info">Email:{{ $unblockRequest->user->email}}</p>
      <p class="card-info">Content ID:{{ $unblockRequest->content->id}}</p>
      <p class="card-info">Reason to Unblock:{{ $unblockRequest->description}}</p>
      <a href="{{ '/reviewcontent/' . $unblockRequest->id }}" class="card-link">Review Content</a>
  </div>
  @endforeach
  <div class="d-flex justify-content-center mt-4">
    @if($unblockRequests->count() > 0)
    {{ $unblockRequests->links('pagination::bootstrap-4') }}
    @else
    <p>No More Unblock Requests Found.</p>
    @endif
  </div>
</div>
@endsection