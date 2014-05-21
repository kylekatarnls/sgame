
@if ($nbPages > 1)
<ul class="pagination">
	<li{{ $currentPage == 1 ? ' class="disabled"' : '' }}>
		<a href="{{ str_replace('%d', $currentPage-1, $pageUrl) }}">{{ §('pagination.previous'/*§&laquo; Précédents§*/) }} </a>
	</li>
	@for ($i = 1; $i <= $nbPages; $i++)
		<li{{ $currentPage == $i ? ' class="active"' : '' }}>
			<a href="{{ str_replace('%d', $i, $pageUrl) }}">{{ $i }}</a>
		</li>
	@endfor
	<li{{ $currentPage == $nbPages ? ' class="disabled"' : '' }}>
		<a href="{{ str_replace('%d', $currentPage+1, $pageUrl) }}">{{ §('pagination.next'/*§Suivants &raquo;§*/) }} </a>
	</li>
</ul>
@endif
