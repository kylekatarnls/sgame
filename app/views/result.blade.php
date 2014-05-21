@extends('layouts.insearch')

@section('title')
	{{ §('global.results.title'/*§:title - Résultats§*/, array('title' => §('global.title'/*§InSearch§*/))) }}
@stop

@section('content')

	<h1 class="results">{{ §('global.results.count'/*§{0}:title - Aucun résultat|{1}:title - Un résultat|[2,Inf]:title - :count résultats§*/, $nbResults, array( 'title' => §('global.title'/*§InSearch§*/) )) }}</h1>

	@include('includes.searchbar')

	@foreach($results as $result)
		<h3>
			<a class="visited" href="{{ $result->url }}"><span class="glyphicon glyphicon-eye-open"></span></a>
			{{ $result->link($result->title) }}
		</h3>
		<p>
			<span class="badge" title="{{ §('global.popularity'/*§Popularité§*/) }}">{{ $result->count }}</span>
			{{ $result->content }}<br>
			{{ $result->link($result->urlAndLanguage, array('class' => 'source')) }}
		</p>
	@endforeach

	@include('includes.pagination')

@stop