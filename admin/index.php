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
$page[ 'title' ] .= $page[ 'title_separator' ].'What\'s on your mind?';
$page[ 'page_id' ] = 'home';
databaseConnect();

$htmlMsg = "";
$loginId = currentUser();
$qry = "SELECT * FROM section where InstSsn IN (
	SELECT Ssn FROM faculty where LoginId = '{$loginId}');";
$result = @mysql_query($qry) or die('<pre>' . mysql_error() . '</pre>' );
if( $result && mysql_num_rows( $result ) >= 1 ) {
	$htmlMsg .= "<ul>";
	while ( $row = mysql_fetch_assoc ( $result ) ) {
		$c_qry = "SELECT * FROM course where CNo = '{$row['CNo']}';";
		$c_result = @mysql_query($c_qry) or die('<pre>' . mysql_error() . '</pre>' );
		$c_row = mysql_fetch_assoc ( $c_result );
		$htmlMsg .= "<li><b> {$c_row['CName']} - {$row['CNo']}.{$row['SecNo']} {$row['SemYear']}</b> ({$row['CRN']}) [<a href=\"course.php?crn={$row['CRN']}&mode=view\">View</a>]</li>";
	}
	$htmlMsg .= "</ul>";
} else {
}

$page[ 'body' ] .= "
	<div class=\"body_padded\">
	<h2>Couses</h2>
	<div class=\"vulnerable_code_area\">
	{$htmlMsg}
	</div>
	<div class=\"clear\"></div>
	<br />
	</div>";


htmlEcho( $page );
?>
