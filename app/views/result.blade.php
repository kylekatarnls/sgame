@extends('layouts.insearch')

@section('title')
	@parent
	- Result
@stop

@section('content')

	<h1>InSearch - Result</h1>

	@include('includes.searchbar')

	@foreach ($results as $result)
		<h2>{{ e($result->title) }}</h2>
		<p><a href="/out/{{ urlencode($result->url) }}">{{ e($result->url) }}</a></p>
		<p>{{ e($result->content) }}</p>
	@endforeach

	@include('includes.pagination')

@stop