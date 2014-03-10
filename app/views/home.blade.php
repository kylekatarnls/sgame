@extends('layouts.insearch')

@section('content')

	<h1>{{ §('global.title') }}</h1>

	@if(isset($url))
		@switch($state)
			@case(Crawler::NOT_FOUND)
				@include('includes.alert', array('type' => 'danger', 'message' => 'global.url-not-found', 'replace' => array('url' => '<strong>' . $url . '</strong>' )))
			@break
			@case(Crawler::DUPLICATED)
				@include('includes.alert', array('type' => 'warning', 'message' => 'global.duplicated-content', 'replace' => array('url' => '<strong>' . $url . '</strong>' )))
			@break
			@default
				@include('includes.alert', array('message' => 'global.url-' . ($state === Crawler::ADDED ? 'added' : 'updated'), 'replace' => array('url' => '<strong>' . $url . '</strong>' )))
			@break
		@endswitch
	@endif

	@include('includes.searchbar', array('q' => ''))

@stop