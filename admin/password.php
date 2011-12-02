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

$page = pageNewGrab();
$page[ 'title' ] .= $page[ 'title_separator' ].'Change Password';
$page[ 'page_id' ] = 'chngpwd';
databaseConnect();

$htmlMsg = "";
$loginId = currentUser();

$iconDir = WEB_PAGE_TO_ROOT.'core/theming/images/icons/';
$htmlMsg = "";
$heading = "Change Password";

if ( isset ( $_POST['ChngPwd'] ) ) {
	$uname = isset ( $_POST['username'] ) ? $_POST['username'] : "";
	$pwd1 = isset ( $_POST['pwd1'] ) ? $_POST['pwd1'] : "";
	$pwd2 = isset ( $_POST['pwd2'] ) ? $_POST['pwd2'] : "";
	$uname = stopSQLi ( $uname );
	$pwd1 = stopSQLi ( $pwd1 );
	$pwd2 = stopSQLi ( $pwd2 );

	if ( $uname == "" || $pwd1 == "" || $pwd2 == "" ) {
		messagePush ( "All fields are compulsary" );
		redirectPage ( "password.php" );
	}
	if ( $pwd1 != $pwd2 ) {
		messagePush ( "Passwords must match");
		redirectPage ( "password.php" );
	}
	$qry = "SELECT * FROM users WHERE LoginId='{$uname}'";
	$result = @mysql_query ( $qry ) or die ( mysql_error() );
	if ( mysql_num_rows ( $result ) != 1 ) {
		messagePush ( "Invalid username" );
		redirectPage ( "password.php" );
	}
	$pwd1 = md5 ( $pwd1 );
	$qry = "UPDATE users SET password='{$pwd1}' WHERE LoginId='{$uname}'";
	$result = @mysql_query ( $qry ) or die ( mysql_error() );
	messagePush ( "Password for {$uname} successfully updated!" );
	redirectPage ( "password.php" );
} else {
	$qry = "SELECT * FROM users";
	$result = mysql_query ( $qry );
	$htmlMsg = "";
	$htmlMsg .= '<form name="form1" action="" method="POST" >';
	$htmlMsg .= '<table style="width: 100%" id="mytable" cellspacing="0" summary="Comments" align="center">';
	$htmlMsg .= '<tr><th colspan="2">Adding Department</th></tr>';
	$htmlMsg .= '<tr>';
	$htmlMsg .= '<th class="spec" scope="row"><center>Choose User</center></th>';
	$htmlMsg .= '<td>';
	$htmlMsg .= '<select name="users" ONCHANGE="document.form1.username.value=document.form1.users.value">';
	$htmlMsg .= '<option value=""> --- Select one --- </option>';
	while ( $result && $row = mysql_fetch_assoc ( $result ) ) {
		$htmlMsg .= '<option value="'. $row['LoginId'] .'">'. $row['LoginId'] .'</option>';
	}
	$htmlMsg .= '</select>';
	$htmlMsg .= ' OR Enter below';
	$htmlMsg .= '</td>';
	$htmlMsg .= '</tr>';
	$htmlMsg .= '<tr>';
	$htmlMsg .= '<th class="spec" scope="row"><center>Username *</center></th>';
	$htmlMsg .= '<td><input type="text" class="inputBox" maxlength="30" name="username" value="" /></td>';
	$htmlMsg .= '</tr>';
	$htmlMsg .= '<tr>';
	$htmlMsg .= '<th class="spec" scope="row"><center>Password *</center></th>';
	$htmlMsg .= '<td><input type="password" class="inputBox" maxlength="30" name="pwd1" value="" /></td>';
	$htmlMsg .= '</tr>';
	$htmlMsg .= '<tr>';
	$htmlMsg .= '<th class="spec" scope="row"><center>Password again *</center></th>';
	$htmlMsg .= '<td><input type="password" class="inputBox" maxlength="30" name="pwd2" value="" /></td>';
	$htmlMsg .= '</tr>';
	$htmlMsg .= '</table>';
	$htmlMsg .= "
		<br />
		<div class=\"join\">
		<input class=\"button\" type=\"submit\" value=\"Change Password\" name=\"ChngPwd\">
		</div>
		";
	$htmlMsg .= '</form>';
}

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
