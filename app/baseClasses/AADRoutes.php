<?php

namespace App\baseClasses;

class AADRoutes {
	public function routes() {
		return array(
			'get_user'       => [ 'method' => 'post', 'action' => 'AADHomeController@getUser' ],
			'update_sidebar' => [ 'method' => 'post', 'action' => 'AADHomeController@updateSidebar' ],
			'add_settings'   => [ 'method' => 'post', 'action' => 'AADHomeController@settings' ],
			'logout'         => [ 'method' => 'post', 'action' => 'AADHomeController@logout' ],
		);
	}
}
