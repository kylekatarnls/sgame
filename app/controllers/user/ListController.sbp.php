<?

namespace User

use User

ListController:\JsonController

	+ run
		$users = array_flatten(User::get(array('email'))->toArray())
		$('#content pre')->html('Liste des utilisateurs : ' . implode(', ', $users))