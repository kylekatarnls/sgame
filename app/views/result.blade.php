@extends('layouts.insearch')

@section('title')
	{{ §('global.results.title', array('title' => §('global.title'))) }}
@stop

@section('content')

	<h1 class="results">{{ §('global.results.count', $nbResults, array( 'title' => §('global.title') )) }}</h1>

	@include('includes.searchbar')

	@foreach($results as $result)
		<h3>
			<a class="visited" href="{{ $result->url }}"><span class="glyphicon glyphicon-eye-open"></span></a>
			<a href="{{ $result->outgoingLink }}">{{ $result->title }}</a>
		</h3>
		<p>
			<span class="badge" title="{{ §('global.popularity') }}">{{ $result->count }}</span>
			{{ $result->content }}<br>
			{{ $result->language }}<br>
			<a href="{{ $result->outgoingLink }}" class="source">{{ e($result->url) }}</a>
		</p>
	@endforeach

	@include('includes.pagination')

@stop