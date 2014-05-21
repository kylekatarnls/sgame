
{{ Form::open(array(
	'url' => '/',
	'method' => 'post'
)) }}
	<div class="input-group">
		{{ Form::text('q', $q, array(
			'class' => 'form-control',
			'autocomplete' => 'off',
			'autofocus' => 'autofocus',
			'placeholder' => §('global.search.placeholder'/*§Chercher une page§*/)
		)) }}
		{{ Form::hidden('resultsPerPage', $resultsPerPage) }}
		<div class="input-group-btn">
			<button class="btn btn-default" type="submit">
				<span class="glyphicon glyphicon-search"></span>
			</button>
		</div>
	</div>
{{ Form::close() }}
