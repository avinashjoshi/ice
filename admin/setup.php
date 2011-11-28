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

pageStartup( array( 'authenticated', 'admin' ) );

$page = pageNewGrab();
$page[ 'title' ] .= $page[ 'title_separator' ].'Setup';
$page[ 'page_id' ] = 'setup';

if( isset( $_POST[ 'create_db' ] ) ) {
	include_once WEB_PAGE_TO_ROOT.'core/includes/MySQL.php';
}

$page[ 'body' ] .= "
	<div class=\"body_padded\">
	<h1>Database setup <img src=\"".WEB_PAGE_TO_ROOT."core/theming/images/spanner.png\"></h1>

	<p>Click on the 'Create / Reset Database' button below to create or reset your database. If you get an error make sure you have the correct user credentials in /config/config.inc.php</p>

	<p>If the database already exists, it will be cleared and the data will be reset.</p>

	<br />

	Backend Database: <b>MySQL</b>

	<br /><br /><br />

	<!-- Create db button -->
	<div class=\"join\">
	<form action=\"setup.php\" method=\"post\">
	<input name=\"create_db\" type=\"submit\" value=\"Create / Reset Database\">
	</form>
	</div>
	</div>
	";

$right = "";

if (isLoggedIn())
	htmlEcho( $page );
else
	noLoginHtmlEcho( $page, $right );

?>
