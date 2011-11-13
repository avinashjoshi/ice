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

$page = pageNewGrab();
$page[ 'title' ] .= $page[ 'title_separator' ].'What\'s on your mind?';
$page[ 'page_id' ] = 'home';
databaseConnect();
/*
if(isset($_POST['btnUpdate'])) {

	if ( $_POST['statusMsg'] == "" ) {
		blobMessagePush( "Status cannot be empty!" );
		blobRedirect( 'index.php' );
	}

	$message = trim($_POST['statusMsg']);

	// Sanitize message input
   $message = stripslashes($message);
   $message = mysql_real_escape_string($message);
   $message = htmlspecialchars($message);

	// Sanitize name input
	$name = stripslashes( $name );
	$name = mysql_real_escape_string($name);

	$query = "INSERT INTO status (user_id, status, date_set) VALUES ('$user_id','$message', NOW());";
	$result = mysql_query($query) or die('<pre>' . mysql_error() . '</pre>' );
}
 */
$page[ 'body' ] .= "
	<div class=\"body_padded\">
		<h2>What's on your mind?</h2>
		<div class=\"vulnerable_code_area\">
			<form method=\"post\" name=\"statusupdate\">
				<input type=\"hidden\" name=\"index.php\" value=\"index.php\" />
				<table width=\"550\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">
					<tr>
						<td><textarea style=\"padding: 5px;\" name=\"statusMsg\" cols=\"60\" rows=\"3\" maxlength=\"140\"></textarea></td>
					</tr>
					<tr>
						<td><input class=\"button\" name=\"btnUpdate\" type=\"submit\" value=\"Blob It!\" > ( Max 140 characters )</td>
					</tr>
				</table>
			</form>
		</div>
		<div class=\"clear\"></div>
		<pre><b>All blob's (friends + me)</b> | <a href=\"".WEB_PAGE_TO_ROOT."profile/view.php?user=$user\" style=\"text-decoration: none;\">My blob's</a></pre>
		<br />
	</div>";


blobHtmlEcho( $page );
?>
