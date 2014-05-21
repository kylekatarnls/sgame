<div class="alert alert-{{ isset($type) ? $type : 'success' }}">
	{{ §(/*@ dynamic @*/$message, $replace ?: array()) }}
</div>