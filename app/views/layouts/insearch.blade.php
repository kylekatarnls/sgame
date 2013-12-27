<!doctype html>
<html lang="{{ Lang::locale() }}">
<head>
	<meta charset="UTF-8">
	<head>
		<title>
			@section('title')
			{{ _('global.title'); }}
			@show
		</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		{{ HTML::style('css/bootstrap.css') }}
		{{ HTML::style('css/insearch.css') }}
		{{ HTML::style('css/bootstrap-responsive.css') }}
		<link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
	</head>
 
	<body>
		<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<div class="dropdown">
						<a class="btn header-option dropdown-toggle" id="choice-per-page" data-toggle="dropdown">
							<span class="glyphicon glyphicon-list"></span>
						</a>
						<div class="header-option">
							<ul class="dropdown-menu" role="menu" aria-labelledby="choice-per-page">
								<li class="dropdown-header">{{ _('global.results-per-page') }}</li>
								@foreach ($choiceResultsPerPage as $choice)
									<li><a href="{{ str_replace('%d', $choice, $resultsPerPageUrl) }}">{{ $choice }}</a></li>
								@endforeach
							</ul>
						</div>
					</div>
 
					<a class="brand" href="#">
						<img src="/img/advanced-search.png" alt="{{ _('global.title'); }}">
					</a>
 
					<div class="nav-collapse collapse">
						<ul class="nav">
							<li><a href="{{{ URL::to('') }}}">{{ _('global.home'); }}</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
 
		<div class="container">
 
			@yield('content')
 
		</div>
 
		{{ HTML::script('js/jquery-1.10.2.min.js') }}
		{{ HTML::script('js/bootstrap.min.js') }}
		{{ HTML::script('js/insearch.js') }}
 
	</body>
</html>