@extends('layouts.insearch')

@section('title')
	@parent
	- Result
@stop

@section('content')

	<h1>InSearch - Result</h1>

	@include('includes.searchbar')

	@foreach ($results as $result)
		<h3>
			<a href="{{ $result->outgoingLink }}">{{ e($result->title) }}</a>
		</h3>
		<p>
			{{ e($result->content) }}<br>
			<a href="{{ $result->outgoingLink }}" class="source">{{ e($result->url) }}</a>
		</p>
	@endforeach

	@include('includes.pagination')

@stop