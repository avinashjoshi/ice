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
$page[ 'title' ] .= $page[ 'title_separator' ].'Faculty';
$page[ 'page_id' ] = 'faculty';
databaseConnect();

$htmlMsg = "";
$loginId = currentUser();

$iconDir = WEB_PAGE_TO_ROOT.'core/theming/images/icons/';
$courseList = "";
$heading = "Faculty List";
$heading .= "
	<div class=\"join\" style=\"float: right;\">
	<a href=\"faculty.php?mode=add\"><input value=\"Add Faculty\" type=\"submit\"></a>
	</div>";

if ( $mode !== "add" && $mode != "" ) {
	messagePush ( "Invalid Mode!" );
	redirectPage ( 'faculty.php' );
}

if ( $mode == "add" ) {
	$flag = true;
	$value = array();
	$value['Ssn'] = isset ( $_POST['Ssn'] ) ? $_POST['Ssn'] : "";
	$value['FName'] = isset ( $_POST['FName'] ) ? $_POST['FName'] : "";
	$value['LName'] = isset ( $_POST['LName'] ) ? $_POST['LName'] : "";
	$value['MInit'] = isset ( $_POST['MInit'] ) ? $_POST['MInit'] : "";
	$value['LoginId'] = isset ( $_POST['LoginId'] ) ? $_POST['LoginId'] : "";
	$value['Date'] = isset ( $_POST['Date'] ) ? $_POST['Date'] : "";
	$value['Month'] = isset ( $_POST['Month'] ) ? $_POST['Month'] : "";
	$value['Year'] = isset ( $_POST['Year'] ) ? $_POST['Year'] : "";
	$value['Address'] = isset ( $_POST['Address'] ) ? $_POST['Address'] : "";
	$value['Salary'] = isset ( $_POST['Salary'] ) ? $_POST['Salary'] : "";
	$value['Email'] = isset ( $_POST['Email'] ) ? $_POST['Email'] : "";
	$value['OfficeLoc'] = isset ( $_POST['OfficeLoc'] ) ? $_POST['OfficeLoc'] : "";
	$value['Position'] = isset ( $_POST['Position'] ) ? $_POST['Position'] : "";
	$value['Phone'] = isset ( $_POST['Phone'] ) ? $_POST['Phone'] : "";
	$value['DNo'] = isset ( $_POST['DNo'] ) ? $_POST['DNo'] : "";
	$value['password'] = isset ( $_POST['password'] ) ? $_POST['password'] : "";
	$value['role'] = isset ( $_POST['role'] ) ? $_POST['role'] : "";
	if ( isset ( $_POST['AddFaculty'] ) ) {
		foreach ( $value as $k => $i ) {
			if ( $i == "" ) {
				messagePush ( "All fields are compulsary! {$k}" );
				redirectPage ( "faculty.php?mode=add" );
				$flag = false;
			}
		}

		if ( $flag == true ) {
			$qry = "START TRANSACTION";
			$result = @mysql_query ( $qry ) or die ( mysql_error() );
			$password = md5 ( $value['password'] );
			$value['BDate'] = $value['Year'] . '-' . $value['Month'] . '-' . $value['Date'];
			$role = $value['role'];
			unset ( $value['Year'] );
			unset ( $value['Month'] );
			unset ( $value['Date'] );
			unset ( $value['password'] );
			unset ( $value['role'] );
			$qry = "INSERT INTO users values ('{$value['LoginId']}', '{$password}', '{$role}')";
			$result = mysql_query ( $qry ) or die ( mysql_error() );
			$attr = "";
			$attr_val = "";
			$last_key = end(array_keys($value));
			foreach ( $value as $k => $i ) {
				if ( $k == "DNo" || $k == "Salary") {
					$attr .= $k;
					$attr_val .= $i;
				} else {
					$attr .= $k;
					$attr_val .= "'{$i}'";
				}
				if ($k != $last_key) {
					$attr .= ', ';
					$attr_val .= ', ';
				}
			}

			$qry = "INSERT INTO faculty ({$attr}) VALUES ({$attr_val})";
			$result = mysql_query ( $qry ) or die ( mysql_error() );
			if ( !$result ) {
				messagePush ( "Oops! Something went wrong!" );
				@mysql_query ( "ROLLBACK" );
				redirectPage ( 'faculty.php?mode=add' );
			}
			@mysql_query ( "COMMIT" );
			messagePush ( "Successfully added faculty" );
			redirectPage ( 'faculty.php' );
		}
	} else {
		$qry = "SELECT * FROM faculty;";
		$result = @mysql_query ( $qry );
		$link = "";
		$heading = "";
		$page[ 'title' ] .= $page[ 'title_separator' ].'Add Faculty';
		$page[ 'page_id' ] = 'addfaculty';
		$htmlMsg .= "<i>Note: There are no checks!</i><br>";
		$htmlMsg .= "<form action=\"{$link}\" method=\"POST\" name=\"formcourse\">";
		$htmlMsg .= '<table style="width: 100%" id="mytable" cellspacing="0" summary="Comments" align="center">';
		$htmlMsg .= '<tr><th colspan="2">Adding Faculty</th></tr>';
		$htmlMsg .= '<tr>';
		$htmlMsg .= '<th class="spec" scope="row"><center>Ssn</center></th>';
		$htmlMsg .= '<td><input type="text" class="inputBox" maxlength="10" name="Ssn" value="" /></td>';
		$htmlMsg .= '</tr>';
		$htmlMsg .= '<tr>';
		$htmlMsg .= '<th class="spec" scope="row"><center>Name</center></th>';
		$htmlMsg .= '<td>';
		$htmlMsg .= '<input type="text" class="inputBox" style="width: 200px; margin-right: 10px;" maxlength="30" name="FName" value="First" />';
		$htmlMsg .= '<input type="text" class="inputBox" style="width: 50px; margin-right: 10px;" maxlength="30" name="MInit" value="Middle" />';
		$htmlMsg .= '<input type="text" class="inputBox" maxlength="30" style="width: 200px;" name="LName" value="Last" />';
		$htmlMsg .= '</td>';
		$htmlMsg .= '</tr>';
		$htmlMsg .= '<tr>';
		$htmlMsg .= '<th class="spec" scope="row"><center>Login ID</center></th>';
		$htmlMsg .= '<td><input type="text" class="inputBox" maxlength="10" name="LoginId" value="" /></td>';
		$htmlMsg .= '</tr>';
		$htmlMsg .= '<tr>';
		$htmlMsg .= '<th class="spec" scope="row"><center>Password</center></th>';
		$htmlMsg .= '<td><input type="password" class="inputBox" maxlength="20" name="password" value="" /></td>';
		$htmlMsg .= '</tr>';
		$htmlMsg .= '<tr>';
		$htmlMsg .= '<th class="spec" scope="row"><center>Role</center></th>';
		$htmlMsg .= '<td> <select name="role">';
		$htmlMsg .= '<option value="">--- Select one ---</option>';
		$htmlMsg .= '<option value="faculty">Faculty</option>';
		$htmlMsg .= '<option value="admin">Admin</option>';
		$htmlMsg .= '</select> </td>';
		$htmlMsg .= '</tr>';
		$htmlMsg .= '<tr>';
		$htmlMsg .= '<th class="spec" scope="row"><center>Birth Date</center></th>';
		$htmlMsg .= '<td>';
		$htmlMsg .= '<input type="text" class="inputBox" style="width: 50px; margin-right: 10px;" maxlength="2" name="Month" value="MM" />';
		$htmlMsg .= '<input type="text" class="inputBox" style="width: 50px; margin-right: 10px;" maxlength="2" name="Date" value="DD" />';
		$htmlMsg .= '<input type="text" class="inputBox" maxlength="4" style="width: 50px;" name="Year" value="YYYY" />';
		$htmlMsg .= '</td>';
		$htmlMsg .= '</tr>';
		$htmlMsg .= '<tr>';
		$htmlMsg .= '<th class="spec" scope="row"><center>Address<center></th>';
		$htmlMsg .= '<td><textarea  class="inputBox" rows="4" cols="50" name="Address"></textarea></td>';
		$htmlMsg .= '</tr>';
		$htmlMsg .= '<tr>';
		$htmlMsg .= '<th class="spec" scope="row"><center>Sex</center></th>';
		$htmlMsg .= '<td> <select name="Sex">';
		$htmlMsg .= '<option value="">--- Select one ---</option>';
		$htmlMsg .= '<option value="M">Male</option>';
		$htmlMsg .= '<option value="F">Female</option>';
		$htmlMsg .= '</select> </td>';
		$htmlMsg .= '</tr>';
		$htmlMsg .= '<tr>';
		$htmlMsg .= '<th class="spec" scope="row"><center>Salary</center></th>';
		$htmlMsg .= '<td><input type="text" class="inputBox" maxlength="10" name="Salary" value="" /></td>';
		$htmlMsg .= '</tr>';
		$htmlMsg .= '<tr>';
		$htmlMsg .= '<th class="spec" scope="row"><center>Email</center></th>';
		$htmlMsg .= '<td><input type="text" class="inputBox" maxlength="50" name="Email" value="" /></td>';
		$htmlMsg .= '</tr>';
		$htmlMsg .= '<tr>';
		$htmlMsg .= '<th class="spec" scope="row"><center>Office Loc</center></th>';
		$htmlMsg .= '<td><input type="text" class="inputBox" maxlength="20" name="OfficeLoc" value="" /></td>';
		$htmlMsg .= '</tr>';
		$htmlMsg .= '<tr>';
		$htmlMsg .= '<th class="spec" scope="row"><center>Position</center></th>';
		$htmlMsg .= '<td><input type="text" class="inputBox" maxlength="30" name="Position" value="" /></td>';
		$htmlMsg .= '</tr>';
		$htmlMsg .= '<tr>';
		$htmlMsg .= '<th class="spec" scope="row"><center>Phone<br><small>10 digits</small></center></th>';
		$htmlMsg .= '<td><input type="text" class="inputBox" maxlength="10" name="Phone" value="" /></td>';
		$htmlMsg .= '</tr>';
		$htmlMsg .= '<tr>';
		$htmlMsg .= '<th class="spec" scope="row"><center>Department</center></th>';
		$htmlMsg .= '<td> <select name="DNo">';
		$htmlMsg .= '<option value=""> --- Select one --- </option>';
		$d_qry ="SELECT * FROM department";
		$d_result = @mysql_query ( $d_qry );
		while ( $d_result && $d_row = mysql_fetch_assoc ( $d_result ) ) {
			$htmlMsg .= '<option value="'. $d_row['DNumber'] .'">'. $d_row['DName'] .'</option>';
		}
		$htmlMsg .= '</select> </td>';
		$htmlMsg .= '</tr>';
		$htmlMsg .= "</table><br />";
		$htmlMsg .= "
			<br />
			<div class=\"join\">
			<input class=\"button\" type=\"submit\" value=\"Add Faculty\" name=\"AddFaculty\">
			<a href=\"faculty.php\"><input value=\"Back\" type=\"submit\"></a>
			</div>
			";
		$htmlMsg .= "</form>";
	}
} else {
	$qry = "SELECT * FROM faculty as F, department as D WHERE F.DNo=D.DNumber;";
	$result = @mysql_query ( $qry ) or die ( mysql_error() );
	if ( $result && mysql_num_rows ( $result ) >= 1 ) {
		$courseList .= '<table style="width: 100%" id="mytable" cellspacing="0" summary="Comments" align="center">';
		$courseList .= '<tr><th>Name</th><th><center>Login</center></th><th>Office</th><th>Department</th><th>Position</th></tr>';
		while ( $row = mysql_fetch_assoc ( $result ) ) {
			$courseList .= '<tr>';
			$courseList .= '<td>' . $row['FName'] . ' '. $row['LName'] .'</td>';
			$courseList .= '<td>' . $row['LoginId'] . '</td>';
			$courseList .= '<td>' . $row['OfficeLoc'] . '</td>';
			$courseList .= '<td align="center">' . $row['DName'] . '</td>';
			$courseList .= '<td>' . $row['Position'] . '</td>';
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
