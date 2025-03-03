
@extends('layouts.app')
@section('pagename')
Questions with the tag [{{ $tag_title }}]
@endsection 

@section('style')
<link href="{{ asset('css/home.css') }}" rel="stylesheet">
<link href="{{ asset('css/tag.css') }}" rel="stylesheet">
<link href="{{ asset('css/search.css') }}" rel="stylesheet">
<link href="{{ asset('css/question_card.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
<link href="{{ asset('css/pagination.css') }}" rel="stylesheet">

@endsection

@section('og')
  <meta property="og:title" content="Questions with the tag [{{ $tag_title }}]" />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="{{ url('/tag/'.$tag_id) }}" />
  <meta property="og:description" content="Questions with the tag [{{ $tag_title }}]" />
  <meta property="og:image" content="{{ url('/images/logo.png') }}" />
@endsection

@section('title', 'content')

@section('content')
<div class="sidebar">
  <a href="/home">Home Page</a>
  <a href="/feed">Feed</a>
  <a class="active" href="{{'/tags'}}">Tags</a>
  <a href="{{'/questions'}}">Questions</a>
  <a href="{{'/users'}}">Users</a>
  @if (Auth::check() && (Auth::user()->usertype == 'admin' || Auth::user()->usertype == 'moderator'))
    <a href="{{'/moderatecontent'}}">Blocked Content</a>
  @endif
</div>

<div>
    <div id="tag_id" hidden>{{$tag_id}}</div>
    <div style="color:white; font-size:0.0001em;">Home</div>
    <div class="realcontent">
        <div id="tag_title"><h1>Questions with the tag [{{ $tag_title }}]</h1></div>
        <input class="searchbar" type="text" id="searchInput" placeholder="Search..." >
        <span class="error" id ="error"></span>
        <label for="sortSelect">Sort By:</label>
        <select id="sortSelect">
            <option value="relevance">Relevance</option>
            <option value="date">Time</option>
            <option value="votes">Votes</option>
        </select>
        <ul id="searchResults">

        </ul>
    <div id="pagination"></div>
</div>
    <script src="{{ asset('js/tagquestionsearch.js') }}" defer></script>
</html>

@endsection