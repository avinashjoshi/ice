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

$page = pageNewGrab();
$page[ 'title' ] .= $page[ 'title_separator' ].'About';
$page[ 'page_id' ] = 'about';
$page[ 'body' ] .= "
	<div class=\"body_padded\">
	<h2>About iCE</h2>

	<div class=\"main_body_box\" style=\"width: 95%;\">
	Interactive Course Evaluation<sup>TM</sup> (<i>iCE</i>) makes life of an instructor easier by providing a web inter- face to provide feedback regarding their course.
	<br /><br />
	In many universities, the current method for an instructor to evaluate and assess performance of students in their class is manually in a spread sheet which is sent to the department. This process may be easy for the professor as only one sheet has to be filled. But the department or university would have to struggle to sort and read each and every assessment.
	<br /><br />
	iCE proposes an online solution for course evaluation making the process easier and more cost effective. We can now be more organized in the process. And finally, the upside of having an online system is − no more spread sheet.
	</div>
	</div>";

$right = "";

if (!isLoggedIn())
	noLoginHtmlEcho( $page, $right );
else
	htmlEcho( $page );

?>
