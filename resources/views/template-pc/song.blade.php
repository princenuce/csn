@extends('template-pc.layout')

@section('title', 'Tìm Kiếm Bài Hát')
@section('description', 'Bài Hát ')

@section('main_content')
<div id="info">
    <div class="head">
        <h1>{{ $song->name }}</h1>
    </div>
    <audio controls loop="loop" preload="none">
       <source src="" >
        Browser Anda tidak mendukung HTML5 Audio
    </audio>
</div>
<div id="list">
    <div class="list">
        @foreach($song->related as $song)
        <div class="list_row">
            <div class="row_title">
                <a href="{{ Helper::link($song['id'], $song['slug']) }}">{{ $song['name'] }}</a>
            </div>
            <div class="row_meta">
                &bull; <span>{{ $song['single'] }}</span></span>
            </div>
        </div>
        @endforeach
    </div>
</div>

@stop