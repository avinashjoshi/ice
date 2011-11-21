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

define( 'WEB_PAGE_TO_ROOT', '../' );
require_once WEB_PAGE_TO_ROOT.'core/includes/functions.inc.php';

pageStartup( array( 'authenticated', 'admin' ) );

$mode = isset ( $_GET['mode'] ) ? $_GET['mode'] : "";


$page = pageNewGrab();
$page[ 'title' ] .= $page[ 'title_separator' ].'Department';
$page[ 'page_id' ] = 'dept';
databaseConnect();

$htmlMsg = "";
$loginId = currentUser();

$iconDir = WEB_PAGE_TO_ROOT.'core/theming/images/icons/';
$courseList = "";
$heading = "Department";
$heading .= "
	<div class=\"join\" style=\"float: right;\">
	<a href=\"department.php?mode=add\"><input value=\"Add Department\" type=\"submit\"></a>
	</div>";

if ( $mode !== "add" && $mode != "" ) {
	messagePush ( "Invalid Mode!" );
	redirectPage ( 'department.php' );
}

if ( $mode == "add" ) {
	$dname = isset ( $_POST['dname'] ) ? $_POST['dname'] : "";
	$dhead = isset ( $_POST['deptHead'] ) ? $_POST['deptHead'] : "";
	$location = isset ( $_POST['location'] ) ? $_POST['location'] : "";
	$phone = isset ( $_POST['phone'] ) ? $_POST['phone'] : "";
	if ( isset ( $_POST['AddDept'] ) ) {
		if ( $dname == "" || $dhead == "" || $location == "" || $phone == "" ) {
			messagePush ( "All fields are compulsary!" );
			redirectPage ( "department.php?mode=add" );
		} else {
			$qry = "INSERT INTO department (DName, DeptHead, Location, Phone) VALUES ('{$dname}', '{$dhead}', '{$location}', '{$phone}')";
			$result = @mysql_query ( $qry );
			if ( !$result ) {
				messagePush ( "Oops! Something went wrong!" );
				redirectPage ( 'department.php' );
			}
			messagePush ( "Successfully added department" );
			redirectPage ( 'department.php' );
		}
	} else {
		$qry = "SELECT * FROM faculty;";
		$result = @mysql_query ( $qry );
		$link = "";
		$heading = "";
		$page[ 'title' ] .= $page[ 'title_separator' ].'Add Department';
		$page[ 'page_id' ] = 'adddept';
		$htmlMsg .= "<form action=\"{$link}\" method=\"POST\" name=\"formcourse\">";
		$htmlMsg .= '<table style="width: 100%" id="mytable" cellspacing="0" summary="Comments" align="center">';
		$htmlMsg .= '<tr><th colspan="2">Adding Department</th></tr>';
		$htmlMsg .= '<tr>';
		$htmlMsg .= '<th class="spec" scope="row"><center>Department Name</center></th>';
		$htmlMsg .= '<td><input type="text" class="inputBox" maxlength="30" name="dname" value="" /></td>';
		$htmlMsg .= '</tr>';
		$htmlMsg .= '<tr>';
		$htmlMsg .= '<th class="spec" scope="row"><center>Department Head</center></th>';
		$htmlMsg .= '<td> <select name="deptHead">';
		$htmlMsg .= '<option value=""> --- Select one --- </option>';
		while ( $result && $row = mysql_fetch_assoc ( $result ) ) {
			$htmlMsg .= '<option value="'. $row['Ssn'] .'">'. $row['FName'] . ' ' . $row['LName'] .'</option>';
		}
		$htmlMsg .= '</select> </td>';
		$htmlMsg .= '</tr>';
		$htmlMsg .= '<tr>';
		$htmlMsg .= '<th class="spec" scope="col"><center>Location</center></th>';
		$htmlMsg .= '<td><input type="text" class="inputBox" maxlength="30" name="location" value="" /></td>';
		$htmlMsg .= '</tr>';
		$htmlMsg .= '<tr>';
		$htmlMsg .= '<th class="spec" scope="row"><center>Phone</center></th>';
		$htmlMsg .= '<td><input type="text" class="inputBox" maxlength="10" name="phone" value="" /></td>';
		$htmlMsg .= '</tr>';
		$htmlMsg .= "</table><br />";
		$htmlMsg .= "
			<br />
			<div class=\"join\">
			<input class=\"button\" type=\"submit\" value=\"Add Department\" name=\"AddDept\">
			<a href=\"department.php\"><input value=\"Back\" type=\"submit\"></a>
			</div>
			";
		$htmlMsg .= "</form>";
	}
} else {
	$qry = "SELECT * FROM department, faculty WHERE department.DeptHead=faculty.Ssn;";
	$result = @mysql_query ( $qry ) or die ( mysql_error() );
	if ( $result && mysql_num_rows ( $result ) >= 1 ) {
		$courseList .= '<table style="width: 100%" id="mytable" cellspacing="0" summary="Comments" align="center">';
		$courseList .= '<tr><th align="center">Department</th><th>Head</th><th width="100"><center>Location</center></th><th width="100">Phone</th></tr>';
		while ( $row = mysql_fetch_assoc ( $result ) ) {
			$link = WEB_PAGE_TO_ROOT.'admin/department.php?course='.$row['DNumber'];
			$courseList .= '<tr>';
			$courseList .= '<td align="center">' . $row['DName'] . '</td>';
			$courseList .= '<td>' . $row['FName'] . ' '. $row['LName'] .'</td>';
			$courseList .= '<td>' . $row['Location'] . '</td>';
			$courseList .= '<td>' . $row['Phone'] . '</td>';
			$courseList .= '</tr>';
		}
		$courseList .= "</table>";
	}
}

$htmlMsg .= $courseList;

$page[ 'below_msg' ] .= "
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
