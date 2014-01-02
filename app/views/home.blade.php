@extends('layouts.insearch')

@section('content')

	<h1>{{ §('global.title') }}</h1>

	@if(isset($url))
		@if($state === Crawler::NOT_FOUND)
			@include('includes.alert', array('type' => 'danger', 'message' => 'global.url-not-found', 'replace' => array('url' => '<strong>' . $url . '</strong>' )))
		@elseif($state === Crawler::DUPLICATED)
			@include('includes.alert', array('type' => 'warning', 'message' => 'global.duplicated-content', 'replace' => array('url' => '<strong>' . $url . '</strong>' )))
		@else
			@include('includes.alert', array('message' => 'global.url-' . ($state === Crawler::ADDED ? 'added' : 'updated'), 'replace' => array('url' => '<strong>' . $url . '</strong>' )))
		@endif
	@endif

	@include('includes.searchbar', array('q' => ''))

@stop