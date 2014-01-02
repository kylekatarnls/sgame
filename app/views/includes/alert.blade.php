<div class="alert alert-{{ isset($type) ? $type : 'success' }}">
	{{ §($message, $replace ?: array()) }}
</div>