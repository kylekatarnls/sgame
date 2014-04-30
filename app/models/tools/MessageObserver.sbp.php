<?

MessageObserver

	+ saved $message

		Cache::forever('message-canal-' . $message->canal, microtime(true))