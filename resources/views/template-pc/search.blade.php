@extends('template-pc.layout')

@section('title', 'Tìm Kiếm Bài Hát')
@section('description', 'Bài Hát ')

@section('main_content')
<div id="list">
	<h1 class="title">Kết quả: <span>{{ $query }}</span></h1>
	<div class="list">
		@foreach($data as $song)
		<div class="list_row">
			<div class="row_title">
				<a href="{{ Helper::link($song['id'], $song['slug']) }}">{{ $song['name'] }}</a>
			</div>
			<div class="row_meta">
				&bull; <span>{{ $song['single'] }}</span>
				&bull; <span>{{ number_format($song['listen'], 0, '.', '.') }} lượt tải </span>
				&bull; <span>{{ $song['duration'] }}</span>
			</div>
		</div>
		@endforeach
	</div>
</div>

@stop