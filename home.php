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

pageStartup( array( 'authenticated', 'faculty' ) );

$page = pageNewGrab();
$page[ 'title' ] .= $page[ 'title_separator' ].'Your Courses';
$page[ 'page_id' ] = 'home';
databaseConnect();

$htmlMsg = "";
$loginId = currentUser();
$qry = "SELECT * FROM section where InstSsn IN (
	SELECT Ssn FROM faculty where LoginId = '{$loginId}');";
$result = @mysql_query($qry) or die('<pre>' . mysql_error() . '</pre>' );
if( $result && mysql_num_rows( $result ) >= 1 ) {
	$htmlMsg = '<table style="width: 100%" id="mytable" cellspacing="0" summary="Comments" align="center">';
	$htmlMsg .= '<tr><th>CRN</th><th>Course</th><th>Course Name</th><th>Section</th><th>Year</th></tr>';
	while ( $row = mysql_fetch_assoc ( $result ) ) {
		$c_qry = "SELECT * FROM course where CNo = '{$row['CNo']}';";
		$c_result = @mysql_query($c_qry) or die('<pre>' . mysql_error() . '</pre>' );
		$c_row = mysql_fetch_assoc ( $c_result );
		$htmlMsg .= '<tr>';
		$htmlMsg .= '<td><a href="course.php?crn='.$row['CRN'].'&mode=view\">' . $row['CRN'] . '</a></td>';
		$htmlMsg .= '<td>' . $row['CNo'] . '</td>';
		$htmlMsg .= '<td>' . $c_row['CName'] . '</td>';
		$htmlMsg .= '<td>' . $row['SecNo'] . '</td>';
		$htmlMsg .= '<td>' . $row['SemYear'] . ' ' . $row['SemTime'] . '</td>';
		$htmlMsg .= '<tr>';
		//$htmlMsg .= "<li><b> {$c_row['CName']} - {$row['CNo']}.{$row['SecNo']} {$row['SemYear']}{$row['SemTime']}</b> ({$row['CRN']}) [<a href=\"course.php?crn={$row['CRN']}&mode=view\">View</a>]</li>";
	}
	$htmlMsg .= "</Table>";
} else {
	$htmlMsg = "Oops! You have not taken any course yet!<br />";
}
if ( isDeptHead() ) {
	$htmlMsg .= "<br /><h3>Courses in your Department:<br /></h3>";
	$loginId = currentUser();
	$qry = "SELECT DNo from faculty where LoginId = '{$loginId}';";
	$result = @mysql_query($qry) or die('<pre>' . mysql_error() . '</pre>' );
	$row = mysql_fetch_assoc ( $result );
	$qry = "SELECT * FROM section, faculty where faculty.Ssn = section.InstSsn AND CNo IN (
		SELECT CNo FROM course where DeptNo = '{$row['DNo']}');";
	$result = @mysql_query($qry) or die('<pre>' . mysql_error() . '</pre>' );
	if( $result && mysql_num_rows( $result ) >= 1 ) {
		$htmlMsg .= '<table style="width: 100%" id="mytable" cellspacing="0" summary="Comments" align="center">';
		$htmlMsg .= '<tr><th>CRN</th><th>Course</th><th>Course Name</th><th>Section</th><th>Year</th><th>Instructor</th></tr>';
		while ( $row = mysql_fetch_assoc ( $result ) ) {
			$c_qry = "SELECT * FROM course where CNo = '{$row['CNo']}';";
			$c_result = @mysql_query($c_qry) or die('<pre>' . mysql_error() . '</pre>' );
			$c_row = mysql_fetch_assoc ( $c_result );
			$htmlMsg .= '<tr>';
		$htmlMsg .= '<td><a href="course.php?crn='.$row['CRN'].'&mode=view\">' . $row['CRN'] . '</a></td>';
			$htmlMsg .= '<td>' . $row['CNo'] . '</td>';
			$htmlMsg .= '<td>' . $c_row['CName'] . '</td>';
			$htmlMsg .= '<td>' . $row['SecNo'] . '</td>';
			$htmlMsg .= '<td>' . $row['SemYear'] . ' ' . $row['SemTime'] . '</td>';
			$htmlMsg .= '<td>' . $row['FName'] . ' ' . $row['LName'] . '</td>';
			$htmlMsg .= '<tr>';
		}
		$htmlMsg .= "</table>";
	}
	$htmlMsg .= "";
}

$page[ 'body' ] .= "
	<div class=\"body_padded\">
	<h2>Your Couses</h2>
	<div class=\"content\">
		{$htmlMsg}
		</div>
		<!--
		<div class=\"vulnerable_code_area\">
		</div>
		-->
		<div class=\"clear\"></div>
		<br />
		</div>";


htmlEcho( $page );
?>
