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

/*
 * This file will list all the CLO's for a particular
 * course taken from url get variable "course"
 */

define( 'WEB_PAGE_TO_ROOT', '' );
require_once WEB_PAGE_TO_ROOT.'core/includes/functions.inc.php';

pageStartup( array( 'authenticated', 'faculty' ) );

$course = isset ( $_GET['course'] ) ? $_GET['course'] : "";
$mode = isset ( $_GET['mode'] ) ? $_GET['mode'] : "";


$page = pageNewGrab();
$page[ 'title' ] .= $page[ 'title_separator' ].'Course List & CLO';
$page[ 'page_id' ] = 'courseclo';
databaseConnect();

$htmlMsg = "";
$loginId = currentUser();

//$qry = "SELECT * FROM section WHERE CRN='{$crn}' AND InstSsn = (
//	SELECT Ssn FROM faculty WHERE LoginId='{$loginId}');";
//$result = @mysql_query ( $qry ) or die (mysql_error());

$iconDir = WEB_PAGE_TO_ROOT.'core/theming/images/icons/';
$courseList = "";
$heading = "Courses & CLO's";
$heading .= "
	<div class=\"join\" style=\"float: right;\">
	<a href=\"course.php?mode=add\"><input value=\"Add Course\" type=\"submit\"></a>
	</div>";

if ( $mode == "add" ) {
	/*
	 * Just adding a course no CLO's
	 */
	if ( $course == "" ) {
		$htmlMsg = "adding";
	} else {
		$mode = isset ( $_GET['row'] ) ? $_GET['row'] : "";
		$htmlMsg = "adding";
	}
} else if ( isset ( $_POST['SubmitChng'] ) ) {
	$postCourse = isset ( $_POST['course'] ) ? $_POST['course'] : "";
	if ( $postCourse == "" ) {
		messagePush ( "Oops Looks like something went wrong!" );
		redirectPage( 'course.php' );
	}
	$count = isset ( $_POST['count'] ) ? $_POST['count'] : "";
	while ( $count ) {
		$qry = "UPDATE clo SET CLO='{$_POST[$count]}' WHERE CLO_No={$count} AND CNo='{$postCourse}';";
		$result = @mysql_query ( $qry ) or die ( mysql_error() );
		$count = $count - 1;
	}
	messagePush ( "Updated Successfully!" );
	redirectPage ('course.php?course='.$postCourse.'&mode=view');
} else if ( $mode != "add" && $mode != "view" && $mode != "edit" && $mode != "" ) {
	messagePush ( "Invalid mode" );
	redirectPage( 'course.php' );
} else if ( $mode == "view" ) {
	if ( $course == "" ) {
		messagePush ( "You must enter a course Number!" );
		redirectPage( 'course.php' );
	}

	$qry = "SELECT * FROM course WHERE CNo = '{$course}';";
	$result = @mysql_query ( $qry ) or die ( mysql_error() );
	if ( $result && mysql_num_rows ( $result ) != 1 ) {
		messagePush ( "Wrong course number!" );
		redirectPage ( "course.php" );
	}
	$row = mysql_fetch_assoc ( $result );
	$heading = "{$row['CName']} ({$row['CNo']})";

	$qry = "SELECT * FROM clo WHERE CNo = '{$course}';";
	$result = @mysql_query ( $qry ) or die ( mysql_error() );
	if ( $result && mysql_num_rows ( $result ) >= 1 ) {
		$courseList .= '<table style="width: 100%" id="mytable" cellspacing="0" summary="Comments" align="center">';
		$courseList .= '<tr><th align="center" width="100px">CLO Number</th><th><center>CLO Description</center></th></tr>';
		while ( $row = mysql_fetch_assoc ( $result ) ) {
			$courseList .= '<tr>';
			$courseList .= '<td align="center">' . $row['CLO_No'] . '</td>';
			$courseList .= '<td>' . $row['CLO'] . '</td>';
			$courseList .= '</tr>';
		}
		$courseList .= "</table>";
	}
	$courseList .= "
		<br />
		<div class=\"join\">
		<a href=\"course.php?course={$course}&mode=edit\"><input value=\"Edit\" type=\"submit\"></a>
		<a href=\"course.php\"><input value=\"Back\" type=\"submit\"></a>
		</div>";
} else if ( $mode == "edit" ) {
	if ( $course == "" ) {
		messagePush ( "You must enter a course Number!" );
		redirectPage( 'course.php' );
	}

	$qry = "SELECT * FROM course WHERE CNo = '{$course}';";
	$result = @mysql_query ( $qry ) or die ( mysql_error() );
	if ( $result && mysql_num_rows ( $result ) != 1 ) {
		messagePush ( "Wrong course number!" );
		redirectPage ( "course.php" );
	}
	$row = mysql_fetch_assoc ( $result );
	$heading = "{$row['CName']} ({$row['CNo']})";

	$link = WEB_PAGE_TO_ROOT.'course.php';

	$courseList .= "<form action=\"{$link}\" method=\"post\" name=\"form\">";

	$count = 0;
	$qry = "SELECT * FROM clo WHERE CNo = '{$course}';";
	$result = @mysql_query ( $qry ) or die ( mysql_error() );
	if ( $result && mysql_num_rows ( $result ) >= 1 ) {
		$courseList .= '<table style="width: 100%" id="mytable" cellspacing="0" summary="Comments" align="center">';
		$courseList .= '<tr><th align="center" width="100px">CLO Number</th><th><center>CLO Description</center></th></tr>';
		while ( $row = mysql_fetch_assoc ( $result ) ) {
			$count = $count +1;
			$courseList .= '<tr>';
			$courseList .= '<td align="center">' . $row['CLO_No'] . '</td>';
			$courseList .= '<td><input type="text" class="inputBox" maxlength="250" name="'. $row['CLO_No'] .'" value="'. $row['CLO']  .'"></td>';
			$courseList .= '</tr>';
		}
		$courseList .= "</table>";
	}
	$courseList .= "
		<br />
		<div class=\"join\">
		<input class=\"button\" type=\"submit\" value=\"Submit Changes\" name=\"SubmitChng\">
		<a href=\"course.php\"><input value=\"Back\" type=\"submit\"></a>
		<input type=\"hidden\" name=\"count\" value=\"{$count}\" />
		<input type=\"hidden\" name=\"course\" value=\"{$course}\" />
		</div>
		";
	$courseList .= "</form>";
} else if ( $mode != "add" ) {
	$qry = "SELECT * FROM course;";
	$result = @mysql_query ( $qry ) or die ( mysql_error() );
	if ( $result && mysql_num_rows ( $result ) >= 1 ) {
		$courseList .= '<table style="width: 100%" id="mytable" cellspacing="0" summary="Comments" align="center">';
		$courseList .= '<tr><th align="center" width="80px">Course ID</th><th>Course Name</th><th colspan="3"><center>Options</center></th></tr>';
		while ( $row = mysql_fetch_assoc ( $result ) ) {
			$link = WEB_PAGE_TO_ROOT.'course.php?course='.$row['CNo'];
			$courseList .= '<tr>';
			$courseList .= '<td align="center">' . $row['CNo'] . '</td>';
			$courseList .= '<td>' . $row['CName'] . '</td>';
			$courseList .= '<td align="center" width="30"><a href="'. $link .'&mode=view"><img width="20px" src="'. $iconDir .'view.png" title="View CLO\'s" alt="View CLO\'s"/></a></td>';
			$courseList .= '<td align="center" width="30"><a href="'. $link .'&mode=edit"><img width="20px" src="'. $iconDir .'edit.png" title="Edit CLO\'s" alt="Add / Edit CLO\'s"/></a></td>';
			$courseList .= '<td align="center" width="30"><a href="'. $link .'&mode=add"><img width="20px" src="'. $iconDir .'add.png" title="Add CLO(s)" alt="Add / Edit CLO\'s"/></a></td>';
			$courseList .= '</tr>';
		}
		$courseList .= "</table>";
	}
}

$htmlMsg .= $courseList;

$page[ 'body' ] .= "
	<div class=\"body_padded\">
	<h2>{$heading}</h2>
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
