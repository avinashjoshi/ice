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
	$user = stripslashes( $user );
	$user = mysql_real_escape_string( $user );

	$pass = $_POST[ 'password' ];
	$pass = stripslashes( $pass );
	$pass = mysql_real_escape_string( $pass );
	$pass = md5( $pass );

	$qry = "SELECT * FROM `users` WHERE user='$user' AND password='$pass';";

	$result = @mysql_query($qry) or die('<pre>' . mysql_error() . '</pre>' );

	if( $result && mysql_num_rows( $result ) == 1 ) {	// Login Successful...
		messagePush( "You have logged in as '".$user."'" );
		blobLogin( $user );
		$row = mysql_fetch_assoc($result);
		if ( $row["isadmin"] == "1" )
			blobAdminLogin();
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
