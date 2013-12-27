
{{ Form::open(array(
	'url' => '/',
	'method' => 'post'
)) }}
	<div class="input-group">
		{{ Form::text('q', $q, array(
			'class' => 'form-control',
			'placeholder' => _('global.search.placeholder')
		)) }}
		<div class="input-group-btn">
			<button class="btn btn-default" type="button">
				<span class="glyphicon glyphicon-search"></span>
			</button>
		</div>
	</div>
{{ Form::close() }}
