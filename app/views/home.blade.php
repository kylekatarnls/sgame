@extends('layouts.insearch')

@section('content')

	<h1>{{ §('global.title') }}</h1>

	@if(isset($url))
		@if($state === Crawler::NOT_FOUND)
			<div class="alert alert-error">
				{{ §('global.url-not-found', array('url' => '<strong>' . $url . '</strong>' )) }}
			</div>
		@else
			<div class="alert alert-success">
				{{ §('global.url-'.($state === Crawler::ADDED ? 'added' : 'updated'), array('url' => '<strong>' . $url . '</strong>' )) }}
			</div>
		@endif
	@endif

	@include('includes.searchbar', array('q' => ''))

@stop