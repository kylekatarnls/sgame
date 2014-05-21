@extends('layouts.insearch')

@section('title')
	{{ §('global.history.title'/*§Historique des recherches§*/, array('title' => §('global.title'/*§InSearch§*/))) }}
@stop

@section('content')

	<h1 class="results">{{ §('global.history.count'/*§{0}:title - Aucune recherche enregistrée|{1}:title - Votre dernière recherche|[2,Inf]:title - :count recherches§*/, $nbResults, array( 'title' => §('global.title'/*§InSearch§*/) )) }}</h1>

	@include('includes.searchbar')
	
	@foreach($results->groupBy(function ($element)
		{
			return $element->created_at->uRecentDate;
		}) as $uRecentDate => $group)
		<h3>{{ $uRecentDate }}</h3>
		@foreach($group as $i => $result)
			<div class="history-{{ $i&1 ? 'odd' : 'even' }}">
				<div class="history-time">
					{{ $result->created_at->recentTime }}
				</div>
				<div class="history-count-results">
					{{ §('global.history.results'/*§[0,1]:count résultat|[2,Inf]:count résultats§*/, $result->results) }}
				</div>
				<div>
					<a href="/1/{{ $result->search_query }}">
						{{ $result->search_query }}
					</a>
				</div>
			</div>
		@endforeach
	@endforeach
	
	@include('includes.pagination')

@stop