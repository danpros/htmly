<?php

class trendschau{

	// Return edit tab on post
	public function editLink($p)
	{
		$user = $_SESSION[config("site.url")]['user'];
		$role = user('role', $user);
		if (isset($p->author)) {
			if ($user === $p->author || $role === 'admin') {
				echo '<div class="edit"><a href="' . $p->url . '">View</a></li><li></li></ul></div>';
			}
		} else {
			echo '<div class="tab"><ul class="nav nav-tabs"><li role="presentation" class="active"><a href="' . $p->url . '">View</a></li><li><a href="' . $p->url . '/edit?destination=post">Edit</a></li></ul></div>';
		}
	}
	
}

?>