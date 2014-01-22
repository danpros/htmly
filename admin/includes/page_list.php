<?php
	date_default_timezone_set('Asia/Jakarta');
	config('source', '../../admin/config.ini');

	// Get static page path. Unsorted. 
	function admin_get_static(){

		static $_cache = array();

		if(empty($_cache)){

			// Get the names of all the
			// static page.

			$_cache = glob('../content/static/*.md', GLOB_NOSORT);
		}

		return $_cache;
	}
	
	// Auto generate menu from static page
	function get_page_list() {

		$posts = admin_get_static();
		krsort($posts);
		
		echo '<table>';
		foreach($posts as $index => $v){
		
			echo '<tr>';
			echo '<td>' . $v . '</td>';
			echo '<td><form method="GET" action="action/edit_page.php"><input type="submit" name="submit" value="Edit"/><input type="hidden" name="url" value="../' . $v . '"/></form></td>';
			echo '<td><form method="GET" action="action/delete_page.php"><input type="submit" name="submit" value="Delete"/><input type="hidden" name="url" value="../' . $v . '"/></form></td>';
			echo '</tr>';
				
		}
		echo '</table>';
		
	}

	
?>