<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<head>
		<title>
			@section('title')
			InSearch
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
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
 
					<a class="brand" href="#">Laravel</a>
 
					<div class="nav-collapse collapse">
						<ul class="nav">
							<li><a href="{{{ URL::to('') }}}">Home</a></li>
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
 
	</body>
</html>