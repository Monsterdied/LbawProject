@extends('layouts.app')

@section('style')
  <link href="{{ asset('css/tag.css') }}" rel="stylesheet">
  <link href="{{ asset('css/question_card.css') }}" rel="stylesheet">
  <link href="{{ asset('css/home.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
  <link href="{{ asset('css/pagination.css') }}" rel="stylesheet">
@endsection
@section('pagename')
Feed
@endsection 

@section('og')
    <meta property="og:title" content="Feed" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url('/feed') }}" />
    <meta property="og:description" content="Feed" />
    <meta property="og:image" content="{{ asset('images/icon.png') }}" />
@endsection

@section('title', 'content')

@section('content')
<div class="sidebar">
  <a href="/home">Home Page</a>
  <a href="/feed" class="active">Feed</a>
  <a href="{{'/tags'}}">Tags</a>
  <a href="{{'/questions'}}">Questions</a>
  <a href="{{'/users'}}">Users</a>
  @if (Auth::check() && (Auth::user()->usertype === 'admin' || Auth::user()->usertype === 'moderator'))
    <a href="{{'/moderatecontent'}}">Blocked Content</a>
  @endif
</div>

  <div class="questionslist"> 
  <h2 style="margin-left:1em;">Feed</h2>
  @if ($questions->isEmpty())
    <h3>No questions found Follow tags to have a feed</h3>
  @endif
  @foreach ($questions as $question)


  <div class= "question">
    
    <div class="votes">
      <p class= "answersnum" class=>{{ $question->answernum}} answers</p>
      <p class="votesnum" class=>{{ $question->commentable->content->votes }} votes</p> 
    </div>
    <div class ="content">
    <a href="{{ url('/question/'.$question->id) }}"><h3>{{ $question->title }}</h3></a>
      <div class="questionbottom">
        <div class="tags">
          @foreach($question->Tags() as $tag)
            <div class="tag"><a href="{{ url('/tag/'.$tag->id) }}">{{ $tag->title }}</a></div>
          @endforeach
        </div>  
        <div class="profileinfo">
          <a href="{{ url('/profile/'.$question->userid) }}">{{ $question->commentable->content->user->username }}</a>
          <p>{{ $question->commentable->content->compileddate() }}</p>
        </div>
      </div>  
    </div>
  </div>
@endforeach
{{ $questions->links() }}
</div>

@endsection