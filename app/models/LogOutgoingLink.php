<?php

/*
 * Log de clic sur un lien sortant
 */
class LogOutgoingLink extends Eloquent {

	protected $collection = 'log_outgoing_link';
	static protected $unguarded = ['search_query'];
}

?>