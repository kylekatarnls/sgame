<!doctype html>
<html lang="{{ Lang::locale() }}">
<head>
	<meta charset="UTF-8">
	<head>
		<title>
			@section('title')
				{{ §('global.title'/*§InSearch§*/) }}
			@show
		</title>
		@section('meta')
			<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, target-densitydpi=device-dpi">
			<meta name="apple-mobile-web-app-capable" content="yes">
			<meta name="format-detection" content="telephone=no">
		@show
		@section('css')
			{{ HTML::style('css/bootstrap.min.css') }}
			{{ HTML::style('css/insearch.css') }}
		@show
		@section('link')
			<link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
		@show
		@yield('head')
	</head>

	<body>
		<div id="wrap">
			<div class="navbar navbar-inverse navbar-fixed-top">
				<div class="navbar-inner">
					<div class="container">
						<div class="dropdown">
							<a class="btn header-option dropdown-toggle" id="choice-per-page" data-toggle="dropdown">
								<span class="glyphicon glyphicon-list"></span>
							</a>
							<div class="header-option">
								<ul class="dropdown-menu" role="menu" aria-labelledby="choice-per-page">
									<li class="dropdown-header">{{ §('global.results-per-page'/*§Résultats par page§*/) }}</li>
									@foreach($choiceResultsPerPage as $choice)
										<li><a data-value="{{ $choice }}" href="{{ str_replace('%d', $choice, $resultsPerPageUrl) }}">{{ $choice }}</a></li>
									@endforeach
								</ul>
							</div>
						</div>
						<div class="option-panel">
							<a class="btn header-option">
								<span class="glyphicon glyphicon-plus-sign"></span>
							</a>
							{{ Form::open(array(
								'url' => '/add-url',
								'method' => 'post'
							)) }}
								<div class="input-group">
									{{ Form::text('url', '', array(
										'class' => 'form-control',
										'placeholder' => §('global.add-url'/*§Ajouter une URL§*/)
									)) }}
									<div class="input-group-btn">
										<button class="btn btn-default" type="submit">
											<span class="glyphicon glyphicon-plus"></span>
										</button>
									</div>
								</div>
							{{ Form::close() }}
						</div>

						<a class="brand" href="/" style="float:left;">
							<img src="/img/advanced-search.png" alt="{{ §('global.title'/*§InSearch§*/) }}">
						</a>

						{{ Form::open(array(
							'url' => '/',
							'method' => 'post'
						)) }}
							<div class="btn-group" id="languages">
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
									<img src="/img/flags/{{ Lang::locale() }}.png" width="16" height="16"> &nbsp;
									{{ array_get($languages, Lang::locale(), head($languages)) }}
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu">
									@foreach($languages as $code => $language)
										<li><a href="{{ isset($q) ? $q : '' }}?language={{ $code }}"{{ Lang::locale() === $code ? ' selected="selected"' : '' }}>
										    <img src="/img/flags/{{ $code }}.png" width="16" height="16"> &nbsp;
										    {{ $language }}
										</a></li>
									@endforeach
								</ul>
							</div>
						{{ Form::close() }}
						<div class="nav-collapse collapse">
							<ul class="nav">
								<li><a href="{{{ URL::to('') }}}">{{ §('global.home'/*§Accueil§*/); }}</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>

			<div class="container">

				@yield('content')

			</div>
		</div>

		<div id="footer">
			<p>
				{{ §('global.footer'/*§InSearch | Projet de fin d'année 2014§*/) }}<br>
				<a href="/most-popular/1">{{ §('global.most-popular'/*§Pages les plus populaires§*/) }}</a> |
				<a href="/history/1">{{ §('global.history.link'/*§Mes précédents recherches§*/) }}</a>
			</p>
		</div>

		@section('js')
			{{ HTML::script('js/jquery-1.10.2.min.js') }}
			{{ HTML::script('js/bootstrap.min.js') }}
			{{ HTML::script('js/typeahead.js') }}
			{{ HTML::script('js/insearch.js') }}
		@show

	</body>
</html>