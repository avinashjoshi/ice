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


define( 'WEB_PAGE_TO_ROOT', '' );
require_once WEB_PAGE_TO_ROOT.'core/includes/functions.inc.php';

pageStartup( array( 'authenticated' ) );

databaseConnect();
$role = getRole();

if ( $role == "admin" ) {
	redirectPage ( WEB_PAGE_TO_ROOT.'admin/index.php' );
}

if ( $role == "faculty" ) {
	redirectPage ( WEB_PAGE_TO_ROOT.'home.php' );
}

messagePush ( "You are not allowed access!" );
redirectPage ( WEB_PAGE_TO_ROOT.'logout.php' );

?>
