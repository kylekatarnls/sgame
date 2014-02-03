@extends('layouts.insearch')

@section('title')
	{{ §('global.history.title', array('title' => §('global.title'))) }}
@stop

@section('content')

	<h1 class="results">{{ §('global.history.count', $nbResults, array( 'title' => §('global.title') )) }}</h1>

	@include('includes.searchbar')

	@foreach($results->groupBy(function ($element)
        {
            return $element->created_at->uRecentDate;
        }) as $uRecentDate => $group)
        @foreach($group as $result)
            <h3>{{ $result->search_query }}</h3>
            <pre>{{ $result->created_at->uRecentDate }}</pre>
            <pre>{{ $result->created_at->recentTime }}</pre>
            <pre>{{ $result->created_at->date }}</pre>
            <pre>{{ $result->results }}</pre>
        @endforeach
	@endforeach

	@include('includes.pagination')

@stop