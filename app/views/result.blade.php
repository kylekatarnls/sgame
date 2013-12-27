@extends('layouts.insearch')

@section('title')
	{{ _('global.results.title', array('title' => _('global.title'))) }}
@stop

@section('content')

	<h1>{{ _('global.results.count', $nbResults, array( 'title' => _('global.title') )) }}</h1>

	@include('includes.searchbar')

	@foreach ($results as $result)
		<h3>
			<a href="{{ $result->outgoingLink }}">{{ e($result->title) }}</a>
		</h3>
		<p>
			<span class="badge">{{ $result->count }}</span>
			{{ e($result->content) }}<br>
			<a href="{{ $result->outgoingLink }}" class="source">{{ e($result->url) }}</a>
		</p>
	@endforeach

	@include('includes.pagination')

@stop