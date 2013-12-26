<?php

/*
 * Log de clic sur un lien sortant
 */
class LogOutgoingLink extends Eloquent {

	protected $collection = 'log_outgoing_link';
	protected $fillable = array('search_query', 'crawled_content_id');
}

?>