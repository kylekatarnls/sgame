@extends('layouts.insearch')

@section('title')
	{{ §('global.results.title', array('title' => §('global.title'))) }}
@stop

@section('content')

	<h1 class="results">{{ §('global.results.count', $nbResults, array( 'title' => §('global.title') )) }}</h1>

	@include('includes.searchbar')

	@foreach($results as $result)
		<?php
		echo setlocale(LC_ALL, '0');
		// Lignes d'exemple
		echo '<h3>' .
			$result->search_query .
		'</h3>';
		echo '<pre>';
			var_dump($result->created_at->toDayDateTimeString());
		echo '</pre>';
		echo '<pre>';
			var_dump($result->created_at->diffForHumans());
		echo '</pre>';
		echo '<pre>';
			var_dump($result->results);
		echo '</pre>';
		?>
	@endforeach

	@include('includes.pagination')

@stop