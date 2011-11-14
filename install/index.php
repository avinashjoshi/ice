<?php

/*
 * Interactive Course Evaluation (iCE) makes life of a course instructor easier
 * by providing a web interface to provide feedback regarding their course.
 * This is done as a part of project for CS 6360: Database Design (Fall 2011)
 *
 * License for this project is defined in README
 *
 * Creator: Avinash Joshi <axj107420@utdallas.edu>
 *
 */

define( 'WEB_PAGE_TO_ROOT', '../' );
require_once WEB_PAGE_TO_ROOT.'core/includes/functions.inc.php';

$page = pageNewGrab();
$page[ 'title' ] .= $page[ 'title_separator' ].'Install';
$page[ 'page_id' ] = 'install';

if( isset( $_POST[ 'create_db' ] ) ) {

	if ($DBMS == 'MySQL') {
		include_once WEB_PAGE_TO_ROOT.'core/includes/DBMS/MySQL.php';
	}
	else {
		messagePush( "ERROR: Invalid database selected. Please review the config file syntax." );
		pageReload();
	}

}

$page[ 'body' ] .= "
	<div class=\"body_padded\">
	<h1>Install <i>". getSoftwareName() ."</i> <img src=\"".WEB_PAGE_TO_ROOT."core/theming/images/spanner.png\"></h1>

	Click on the 'Install Now' button below to create or reset your database. If you get an error make sure you have the correct user credentials in /config/config.inc.php

	<p><i><b>Note: If the database already exists, it will be cleared and the data will be reset.</b></i></p>
	<br />
	Backend Database: <b>".$DBMS."</b>
	<br /><br /><br />
	<div class=\"join\">
	<form action=\"\" method=\"post\">
	<input name=\"create_db\" type=\"submit\" value=\"Install Now\">
	</form>
	</div>
	</div>
	";

if ( $installComplete == true ) {
	$page ['below_msg' ] = "
	<div class=\"join\">
	<form action=\"\" method=\"post\">
	<input name=\"create_db\" type=\"submit\" value=\"Install Now\">
	</form>
	</div>
		";
}

$right = "";

if (isLoggedIn())
	htmlEcho( $page );
else
	noLoginHtmlEcho( $page, $right );

?>
