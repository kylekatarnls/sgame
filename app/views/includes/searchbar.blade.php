
{{ Form::open([
	'url' => '/',
	'method' => 'post'
]) }}
	<div class="input-group">
		{{ Form::text('q', $q, [
			'class' => 'form-control',
			'placeholder' => 'Search a page'
		]) }}
		<!--
		<div class="input-group-btn">
			<button type="button" class="btn btn-default square dropdown-toggle" data-toggle="dropdown">
				Results per page <span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				@foreach ($choiceResultsPerPage as $choice)
					<li><a href="{{ str_replace('%d', $choice, $resultsPerPageUrl) }}">{{ $choice }}</a></li>
				@endforeach
			</ul>
		</div>
		-->
		<div class="input-group-btn">
			<button class="btn btn-default" type="button">
				<span class="glyphicon glyphicon-search"></span>
			</button>
		</div>
	</div>
{{ Form::close() }}
