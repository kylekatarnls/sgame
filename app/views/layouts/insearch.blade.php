<!doctype html>
<html lang="{{ Lang::locale() }}">
<head>
	<meta charset="UTF-8">
	<head>
		<title>
			@section('title')
			{{ §('global.title'); }}
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
								<li class="dropdown-header">{{ §('global.results-per-page') }}</li>
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
									'placeholder' => §('global.add-url')
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
						<img src="/img/advanced-search.png" alt="{{ §('global.title'); }}">
					</a>
 
					{{ Form::open(array(
						'url' => '/',
						'method' => 'post',
						'style' => 'float: left; margin: 7px 0 0 10px;'
					)) }}
						<select name="language" class="form-control" onchange="this.form.submit();">
							@foreach(array(
								'en' => 'English',
								'fr' => 'Français'
							) as $code => $language)
								<option value="{{ $code }}"{{ Lang::locale() === $code ? ' selected="selected"' : '' }}>{{ $language }}</option>
							@endforeach
						</select>
					{{ Form::close() }}
					<div class="nav-collapse collapse">
						<ul class="nav">
							<li><a href="{{{ URL::to('') }}}">{{ §('global.home'); }}</a></li>
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