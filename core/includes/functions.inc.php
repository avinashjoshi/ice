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
 * WEB_PAGE_TO_ROOT is used to define the PATH to application's root
 * This is defined in top of every page
 */
if (!defined ('WEB_PAGE_TO_ROOT')) {
	die ('System error- WEB_PAGE_TO_ROOT undefined');
	exit;
}

require_once WEB_PAGE_TO_ROOT.'core/theming/formatting.inc.php';
// Starting a new session
session_start ();

// Include configs
require_once WEB_PAGE_TO_ROOT . 'config/config.inc.php';

// Declare the $html variable
if (!isset ($html)){
	$html = "";
}

// iCE version
function getVersion () {
	return '0.9';
}

//Software name
function getSoftwareName () {
	return 'iCE';
}

// Software release date
function getReleaseDate () {
	return 'May 01 2011';
}

// Start session functions --
function &sessionGrab () {
	if (!isset ($_SESSION['iCE'])) {
		$_SESSION['iCE'] = array();
	}
	return $_SESSION['iCE'];
}

function pageStartup ($pActions) {
	if (in_array ('authenticated', $pActions)) {
		if (!isLoggedIn ()) {
			redirectPage (WEB_PAGE_TO_ROOT.'login.php');
		}
	}
	if (in_array ('faculty', $pActions)) {
		if (!isFaculty ()) {
			redirectPage (WEB_PAGE_TO_ROOT.'index.php');
		}
	}
	if( in_array( 'notauthenticated', $pActions ) ) {
		if( isLoggedIn()){
			messagePush( "You are logged in!" );
			redirectPage( WEB_PAGE_TO_ROOT.'index.php' );
		}
	}
	if( in_array( 'admin', $pActions ) ) {
		if( !isAdmin()){
			messagePush( "You are not admin!" );
			redirectPage( WEB_PAGE_TO_ROOT.'index.php' );
		}
	}
}

function loginUser( $pUsername ) {
	$theSession =& sessionGrab();
	$theSession['username'] = $pUsername;
}

function setDeptHead () {
	$theSession =& sessionGrab();
	$theSession['hod'] = true;
}

function isDeptHead () {
	$theSession =& sessionGrab();
	return (isset ( $theSession['hod'] ) ? true : false);
}

function setRole( $pRole ) {
	$theSession =& sessionGrab();
	$theSession['role'] = $pRole;
}

function getRole() {
	$theSession =& sessionGrab();
	return ( $theSession['role'] );
}

function isLoggedIn() {
	$theSession =& sessionGrab();
	return isset( $theSession['username'] );
}

function isAdmin() {
	$theSession =& sessionGrab();
	if ( $theSession['role'] == "admin" )
		return true;
}

function isFaculty() {
	$theSession =& sessionGrab();
	if ( $theSession['role'] == "faculty" )
		return true;
}

function logoutUser() {
	$theSession =& sessionGrab();
	unset( $theSession['username'] );
	unset( $theSession['role'] );
	unset( $theSession['hod'] );
}

function pageReload() {
	redirectPage( $_SERVER[ 'PHP_SELF' ] );
}

function currentUser() {
	$theSession =& sessionGrab();
	return ( isset( $theSession['username']) ? $theSession['username'] : '') ;
}

function currentName() {
	$theSession =& sessionGrab();
	$database = "";
	if ( $theSession['role'] == "faculty" )
		$database = "faculty";
	if ( $theSession['role'] == "student" )
		$database = "student";
	if ($database == "")
		return "No Name";
	$query = "SELECT * FROM `{$database}` where LoginId = '{$theSession['username']}'";
	$result = mysql_query ( $query );
	$row = mysql_fetch_assoc ( $result );
	return ( $row['FName'] . ' ' . $row['LName'] );
}

/*
 * Used to redirect page to a $pLocation
 */
function redirectPage( $pLocation ) {
	session_commit();
	header( "Location: {$pLocation}" );
	exit;
}

/*
 * Your Howdy/Welcome message next to iCE logo
 */
function getQuote() {
	$user = "Guest";
	$message = "Welcome";
	if ( isLoggedIn() ) {
		$user = currentName();
		//$user = currentUser();
		$message = "Howdy,";
	}
	$quote = "{$message} <font color=\"#99cc33\">{$user}</font> !";
	return $quote;
}

function getQuoteOld() {
	$link = WEB_PAGE_TO_ROOT . 'about.php';
	$link = "<a href=\"$link\" style=\"color: #99cc33;\">Read More</a>";
	$quote = "<font color=\"#99cc33\"><i>".getSoftwareName()."</i></font> makes life of a course instructor easier by providing a web interface to provide feedback regarding their course. ({$link})";
	return ($quote);
}

// Start message functions --
function messagePush( $pMessage ) {
	$theSession =& sessionGrab();
	if( !isset( $theSession[ 'messages' ] ) ) {
		$theSession[ 'messages' ] = array();
	}
	$theSession[ 'messages' ][] = $pMessage;
}

function messagePop() {
	$theSession =& sessionGrab();
	if( !isset( $theSession[ 'messages' ] ) || count( $theSession[ 'messages' ] ) == 0 ) {
		return false;
	}
	return array_shift( $theSession[ 'messages' ] );
}

function messagesPopAllToHtml() {
	$messagesHtml = '';
	while( $message = messagePop() ) {	// TODO- sharpen!
		$messagesHtml .= "<div class=\"message\">{$message}</div>";
	}
	return $messagesHtml;
}
// --END

// To be used on all external links --
function getExternalLinkUrl( $pLink,$text=null ) {
	if (is_null($text)){
		return '<a href="'.$pLink.'" target="_blank">'.$pLink.'</a>';
	} else {
		return '<a href="'.$pLink.'" target="_blank">'.$text.'</a>';
	}
}
// -- END

// To be used on all internal links (opens in same page)--
function getInternalLinkUrl( $pLink,$text=null ) {
	if (is_null($text)){
		return '<a href="'.$pLink.'">'.$pLink.'</a>';
	} else {
		return '<a href="'.$pLink.'">'.$text.'</a>';
	}
}
// -- END

// Database Management --
if ($DBMS == 'MySQL') {
	$DBMS = htmlspecialchars(strip_tags($DBMS));
	$DBMS_errorFunc = 'mysql_error()';
}
else {
	$DBMS = "No DBMS selected.";
	$DBMS_errorFunc = '';
}

$DBMS_connError = '<div align="center">
	<img src="'.WEB_PAGE_TO_ROOT.'core/theming/images/logo.png">
	<pre style="font-size: 16px;">Oops! Looks like you do not have a database setup yet!<br /></pre>
	<pre style="font-size: 16px;">Click <a href="'.WEB_PAGE_TO_ROOT.'install/index.php">here</a> to setup the database.</pre>
	<pre>[Unable to connect to the database: '. mysql_error() .']<br /></pre>
	</div>';

$DBMS_tablesError = '<div align="center">
	<img src="'.WEB_PAGE_TO_ROOT.'core/theming/images/logo.png">
	<pre style="font-size: 16px;">Oops! Looks like there is some problem with your installation!<br /></pre>
	<pre style="font-size: 16px;">Click <a href="'.WEB_PAGE_TO_ROOT.'install/index.php">here</a> to setup the database.</pre>
	<pre>[Unable to connect to the database: '. mysql_error() .']<br /></pre>
	</div>';

/*
 * I have this to check the sanity of current table.
 * Not fully implemented. Can be maede better.
 * Will add later
 */
function databaseCheck() {
	global $_DBC;
	global $DBMS_tablesError;
	$flag = true;
	$listTables = array('users', 'users');

	$qry = "SHOW TABLES FROM {$_DBC['db_database']}";
	$result = @mysql_query($qry) or die('<pre>' . mysql_error() . '</pre>' );

	if( $result && mysql_num_rows( $result ) == 0 ) {
		echo "<title>Oops!</title>";
		die ($DBMS_tablesError);
	}

	$tables = array();
	while ($row = mysql_fetch_row($result)) {
		$tables[] = $row[0];
	}

	foreach ($listTables as $list) {
		if (!in_array($list, $tables)) {
			$flag = false;
		}
	}

	if ($flag == false)
		die ($DBMS_tablesError);
}

/*
 * databaseConnect() is used to just connect to the database 
 * Connection details in /config/config.inc.php
 */
function databaseConnect() {
	global $_DBC;
	global $DBMS;
	global $DBMS_connError;
	if ($DBMS == 'MySQL') {
		/*
		 * mysql_connect() connects to Database using host, username & password as arguments
		 * mysql_select_db() selects the database with the above connection
		 * I have used @ to supress verbose error reports
		 */
		if( !@mysql_connect( $_DBC[ 'db_server' ], $_DBC[ 'db_user' ], $_DBC[ 'db_password' ] )
			|| !@mysql_select_db( $_DBC[ 'db_database' ] ) ) {
				echo "<title>Oops!</title>";
				die( $DBMS_connError );
			}
	}
	databaseCheck();
}
// -- END

/*
 * stopSQLi takes a string and returns string
 * after removing possible SQL Injection
 */
function stopSQLi( $string ) {
	/*
	 * stripslashes() strips slashes in the string
	 * Returns a string with backslashes stripped off
	 */
	$string = stripslashes( $string );
	/*
	 * mysql_real_escape_string prepends backslashes
	 * to the following characters: \x00, \n, \r, \, ', " and \x1a
	 * in $string to prevent SQL Injection
	 */
	$string = mysql_real_escape_string( $string );
	/*
	 * You can use htmlspecialchars()
	 * This function is useful in preventing user-supplied text
	 * from containing HTML markup, such as in a message board or guest book application
	 */
	return $string;
}

?>
