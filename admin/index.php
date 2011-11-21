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
$page[ 'title' ] .= $page[ 'title_separator' ].'Admin Panel';
$page[ 'page_id' ] = 'home';
databaseConnect();

$page[ 'body' ] .= "
	<div class=\"body_padded\">
	<h2>Admin Index</h2>
	<div class=\"vulnerable_code_area\">
	Your admin menu is on the right!
	</div>
	<div class=\"clear\"></div>
	<br />
	</div>";

htmlEcho( $page );
?>
