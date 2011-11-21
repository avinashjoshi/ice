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
pageStartup( array( 'notauthenticated' ) );
global $univLogoImg;

databaseConnect();

if( isset( $_POST[ 'Login' ] ) ) {

	$user = $_POST[ 'netid' ];
	if ( $user == "" ) {
		messagePush ( "You must enter a value for username" );
		redirectPage ( 'login.php' );
	}
	$user = stopSQLi( $user );

	$pass = $_POST[ 'password' ];
	if ( $pass == "" ) {
		messagePush ( "You must enter a value for password" );
		redirectPage ( 'login.php' );
	}
	stopSQLi( $pass );
	$pass = md5( $pass );
	stopSQLi( $pass );

	$qry = "SELECT * FROM `users` WHERE LoginId='$user' AND password='$pass';";

	$result = @mysql_query($qry) or die('<pre>' . mysql_error() . '</pre>' );

	if( $result && mysql_num_rows( $result ) == 1 ) {	// Login Successful...
		$row = mysql_fetch_assoc($result);
		// Students are not allowed to login!
		if ( $row["role"] == "student" ) {
			messagePush ( "You do not have permission to logon!" );
			redirectPage ( 'index.php' );
		}
		messagePush( "You have logged in as '".$user."'" );
		loginUser( $user );
		setRole( $row["role"] );
		if ( $row['role'] == "faculty" ) { 
			$h_qry = "SELECT * FROM faculty as f, department as d WHERE LoginId = '{$user}' AND f.Ssn = d.DeptHead";
			$h_result = @mysql_query ( $h_qry ) or die ( mysql_error() );
			if ( mysql_num_rows ( $h_result ) >= 1 ) {
				setDeptHead();
			}
			/*
			$h_qry = "SELECT * FROM faculty WHERE LoginId = '{$user}'";
			$h_result = @mysql_query ( $h_qry ) or die ( mysql_error() );
			$h_row = mysql_fetch_assoc ( $h_result );
			if ( $h_row['Position'] == "Department Head" ) {
				setDeptHead();
			}
			 */
		}
		redirectPage( 'index.php' );
	}

	// Login failed
	messagePush( "Login failed" );
	redirectPage( 'login.php' );
}

$univLogo = "";
if ($univLogoImg != "") {
	$univLogo = "<div style=\"position:absolute;\">
		<img style=\"width: 250px;\" src=\"".WEB_PAGE_TO_ROOT."core/theming/images/{$univLogoImg}\">
		</div>";
}

$page = pageNewGrab();
$page[ 'title' ] .= $page[ 'title_separator' ].'Login';
$page[ 'page_id' ] = 'login';
$page [ 'onload' ] = "onLoad=\"document.form.netid.focus()\"";
$page[ 'body' ] .= "
{$univLogo}
<div class=\"body_padded\" align=\"center\">
<h2>Signon</h2>

<div class=\"main_body_box\" style=\"width: 350px;\">
<form action=\"login.php\" method=\"post\" name=\"form\"> <fieldset>
<input type=\"hidden\" name=\"login.php\" value=\"login.php\" />
<div style=\"float: left\">
<label for=\"user\">Login ID</label> <input type=\"text\" class=\"loginInput\" size=\"20\" name=\"netid\"><br />
<label for=\"pass\">Password</label> <input type=\"password\" class=\"loginInput\" AUTOCOMPLETE=\"off\" size=\"20\" name=\"password\"><br />

<p align=\"center\"><input class=\"button\" type=\"submit\" value=\"Login\" name=\"Login\">
</div>
</fieldset> </form>
</div>
<p align=\"center\">Forgot password? - Contact system administrator</p>
</div>";

$right = "";

noLoginHtmlEcho( $page, $right );

?>
