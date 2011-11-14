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

function adminLogin() {
	$theSession =& sessionGrab();
	$theSession['admin'] = true;
}

function isLoggedIn() {
	$theSession =& sessionGrab();
	return isset( $theSession['username'] );
}

function isAdmin() {
	$theSession =& sessionGrab();
	return isset( $theSession['admin'] );
}

function logoutUser() {
	$theSession =& sessionGrab();
	unset( $theSession['username'] );
	unset( $theSession['admin'] );
}

function pageReload() {
	redirectPage( $_SERVER[ 'PHP_SELF' ] );
}

function currentUser() {
	$theSession =& sessionGrab();
	return ( isset( $theSession['username']) ? $theSession['username'] : '') ;
}

function redirectPage( $pLocation ) {
	session_commit();
	header( "Location: {$pLocation}" );
	exit;
}

// Start message functions for registration page only --
function regMessagePush( $id, $pMessage ) {
	$theSession =& sessionGrab();
	if( !isset( $theSession[ 'regMessages' ] ) ) {
		$theSession[ 'regMessages' ] = array();
	}
	$theSession[ 'regMessages' ][$id] = $pMessage;
}

function regMessagePop( $id ) {
	$theSession =& sessionGrab();
	if( !isset( $theSession[ 'regMessages' ] ) || count( $theSession[ 'regMessages' ] ) == 0 ) {
		return false;
	}
	$retVal = $theSession[ 'regMessages' ][$id];
	unset($theSession[ 'regMessages' ][$id]);
	return ( $retVal );
}

function messagesRegPopAllToHtml() {
	$messagesHtml = '';
	while( $message = messagePop() ) {	// TODO- sharpen!
		$messagesHtml .= "<div class=\"message\">{$message}</div>";
	}
	return $messagesHtml;
}
// --END

function getQuote() {
	$user = "Guest";
	$message = "Welcome";
	if ( isLoggedIn() ) {
		$user = currentUser();
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

function databaseConnect() {
	global $_DBC;
	global $DBMS;
	global $DBMS_connError;
	if ($DBMS == 'MySQL') {
		if( !@mysql_connect( $_DBC[ 'db_server' ], $_DBC[ 'db_user' ], $_DBC[ 'db_password' ] )
			|| !@mysql_select_db( $_DBC[ 'db_database' ] ) ) {
				echo "<title>Oops!</title>";
				die( $DBMS_connError );
			}
	}
	databaseCheck();
}
// -- END

function stopSQLi( $string ) {
	$string = stripslashes( $string );
	$string = mysql_real_escape_string( $string );
	return $string;
}

?>
