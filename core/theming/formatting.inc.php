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

function &pageNewGrab() {
	$returnArray = array(
		'title' => getSoftwareName().' v'.getVersion().'',
		'title_separator' => ' :: ',
		'body' => '',
		'below_msg' => '',
		'page_id' => '',
		'help_button' => '',
		'source_button' => '',
		'onload' => '',
		'script' => '',
	);
	return $returnArray;
}


function htmlEcho( $pPage ) {

	$menuBlocks = array();

	$menuBlocks['profile'] = array();
	$menuBlocks['profile'][] = array( 'id' => 'viewprofile', 'name' => 'View Profile', 'url' => 'profile/view.php' );
	$menuBlocks['profile'][] = array( 'id' => 'editprofile', 'name' => 'Edit Profile', 'url' => 'profile/edit.php' );
	$menuBlocks['profile'][] = array( 'id' => 'othersprofile', 'name' => 'View Users', 'url' => 'profile/follow.php' );
	$menuBlocks['profile'][] = array( 'id' => 'following', 'name' => 'Following', 'url' => 'profile/following.php' );

	if ( isAdmin()) {
		$menuBlocks['admin'] = array();
		$menuBlocks['admin'][] = array( 'id' => 'setup', 'name' => 'Setup', 'url' => 'setup.php' );
	}

	$menuHtml = '';

	foreach( $menuBlocks as $menuBlock ) {
		$menuBlockHtml = '';
		foreach( $menuBlock as $menuItem ) {
			$selectedClass = ( $menuItem[ 'id' ] == $pPage[ 'page_id' ] ) ? 'selected' : '';
			$fixedUrl = WEB_PAGE_TO_ROOT.$menuItem['url'];
			$menuBlockHtml .= "<li onclick=\"window.location='{$fixedUrl}'\" class=\"{$selectedClass}\"><a href=\"{$fixedUrl}\">{$menuItem['name']}</a></li>";
		}
		$menuHtml .= "<ul>{$menuBlockHtml}</ul>";
	}
	$adminLink = "";
	//Primary Menu
	$pmenuBlocks = array();
	$pmenuBlocks[] = array( 'id' => 'home', 'name' => 'Home', 'url' => '.' );
	if ( isAdmin()) {
		$adminLink = WEB_PAGE_TO_ROOT . 'admin';
		$adminLink = getInternalLinkUrl( $adminLink, "Admin");
		$pmenuBlocks[] = array( 'id' => 'admin', 'name' => 'Admin', 'url' => 'admin' );
	}
	$pmenuBlocks[] = array( 'id' => 'about', 'name' => 'About', 'url' => 'about.php' );
	$pmenuBlocks[] = array( 'id' => 'logout', 'name' => 'Logout', 'url' => 'logout.php' );

	$primaryMenuHtml = '';
	$pmenuBlockHtml = '';
	foreach( $pmenuBlocks as $pmenuItem ) {
		$selectedClass = ( $pmenuItem[ 'id' ] == $pPage[ 'page_id' ] ) ? 'selected' : '';
		$fixedUrl = WEB_PAGE_TO_ROOT.$pmenuItem['url'];
		$pmenuBlockHtml .= "<li onclick=\"window.location='{$fixedUrl}'\" class=\"{$selectedClass}\"><a href=\"{$fixedUrl}\">{$pmenuItem['name']}</a></li>";
	}
	$primaryMenuHtml .= "<ul>{$pmenuBlockHtml}</ul>";

	databaseConnect();
	$quote = getQuote();
	$homepage = WEB_PAGE_TO_ROOT . 'index.php';
	$profilepage = WEB_PAGE_TO_ROOT . 'profile';

	$messagesHtml = messagesPopAllToHtml();
	if( $messagesHtml ) {
		$messagesHtml = "<div class=\"body_padded\">{$messagesHtml}</div>";
	}

	// Send Headers + main HTML code
	Header( 'Cache-Control: no-cache, must-revalidate');		// HTTP/1.1
	Header( 'Content-Type: text/html;charset=utf-8' );		// TODO- proper XHTML headers...
	Header( "Expires: Tue, 23 Jun 2009 12:00:00 GMT");		// Date in the past

	echo "
		<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
		<html xmlns=\"http://www.w3.org/1999/xhtml\">
		<head>
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
		<title>{$pPage['title']}</title>
		<link rel=\"stylesheet\" type=\"text/css\" href=\"".WEB_PAGE_TO_ROOT."core/theming/css/login.css\" />
		<link rel=\"stylesheet\" type=\"text/css\" href=\"".WEB_PAGE_TO_ROOT."core/theming/css/main.css\" />
		<link rel=\"stylesheet\" type=\"text/css\" href=\"".WEB_PAGE_TO_ROOT."core/theming/css/table.css\" />
		<link rel=\"icon\" type=\"\image/ico\" href=\"".WEB_PAGE_TO_ROOT."favicon.ico\" />
		<script type=\"text/javascript\" src=\"".WEB_PAGE_TO_ROOT."core/theming/js/page.js\"></script>
<script type=\"text/javascript\" src=\"".WEB_PAGE_TO_ROOT."core/theming/js/jquery.min.js\"></script>
{$pPage['script']}
</head>

<body {$pPage['onload']} class=\"home\">
<div id=\"header\">
<a href=\"{$homepage}\"><img class=\"header_img\" src=\"".WEB_PAGE_TO_ROOT."core/theming/images/logo.png\" alt=\"ice\" /></a>
<div id=\"quote\">
	{$quote}
	</div>

	<div id=\"primary_menu\">
	{$primaryMenuHtml}
	</div>
	</div>
	<div id=\"wrapper\">
	<div id=\"container\" class=\"rounded-corners\">
	<div id=\"main_menu\">
	<div id=\"profile_info\">
	<div>User Name</div>
	<div>{$adminLink}</div>
	</div>
	<div id=\"main_menu_padded\">
	{$menuHtml}
	</div>
	</div>
	<div id=\"main_body\" class=\"rounded-corners\">
	{$pPage['body']}
	<br />
	<center>
	{$messagesHtml}
	</center>
	<br />
	{$pPage['below_msg']}
	</div>
	<div class=\"clear\">
	</div>
	</div>
	<div id=\"footer\" class=\"rounded-corners\">
	<p>iCE v".getVersion()." is a Free and OpenSource Microblogging client</p>
	</div>
	</body>
	</html>";
}

function noLoginHtmlEcho( $pPage, $right ) {
	$homepage = WEB_PAGE_TO_ROOT . 'index.php';

	$pmenuBlocks = array();
	if ($pPage['page_id'] == "install") {
		$pmenuBlocks[] = array( 'id' => 'home', 'name' => 'Home', 'url' => 'index.php' );
		$pmenuBlocks[] = array( 'id' => 'install', 'name' => 'Install', 'url' => 'install/index.php' );
		$pmenuBlocks[] = array( 'id' => 'about', 'name' => 'About', 'url' => 'about.php' );
	} else {
		$pmenuBlocks[] = array( 'id' => 'home', 'name' => 'Home', 'url' => 'index.php' );
		$pmenuBlocks[] = array( 'id' => 'login', 'name' => 'Login', 'url' => 'login.php' );
		$pmenuBlocks[] = array( 'id' => 'about', 'name' => 'About', 'url' => 'about.php' );
	}
	$primaryMenuHtml = '';
	$pmenuBlockHtml = '';
	foreach( $pmenuBlocks as $pmenuItem ) {
		$selectedClass = ( $pmenuItem[ 'id' ] == $pPage[ 'page_id' ] ) ? 'selected' : '';
		$fixedUrl = WEB_PAGE_TO_ROOT.$pmenuItem['url'];
		$pmenuBlockHtml .= "<li onclick=\"window.location='{$fixedUrl}'\" class=\"{$selectedClass}\"><a href=\"{$fixedUrl}\">{$pmenuItem['name']}</a></li>";
	}
	$primaryMenuHtml .= "<ul>{$pmenuBlockHtml}</ul>";

	$quote = getQuote();

	$messagesHtml = messagesPopAllToHtml();
	if( $messagesHtml ) {
		$messagesHtml = "<div class=\"body_padded\">{$messagesHtml}</div>";
	}

	// Send Headers + main HTML code
	Header( 'Cache-Control: no-cache, must-revalidate');		// HTTP/1.1
	Header( 'Content-Type: text/html;charset=utf-8' );		// TODO- proper XHTML headers...
	Header( "Expires: Tue, 23 Jun 2009 12:00:00 GMT");		// Date in the past

	echo "
		<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
		<html xmlns=\"http://www.w3.org/1999/xhtml\">
		<head>
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
		<title>{$pPage['title']}</title>
		<link rel=\"stylesheet\" type=\"text/css\" href=\"".WEB_PAGE_TO_ROOT."core/theming/css/login.css\" />
		<link rel=\"stylesheet\" type=\"text/css\" href=\"".WEB_PAGE_TO_ROOT."core/theming/css/main.css\" />
		<link rel=\"icon\" type=\"\image/ico\" href=\"".WEB_PAGE_TO_ROOT."favicon.ico\" />
	{$pPage['script']}
	</head>
	<body {$pPage['onload']} class=\"home\">
	<div id=\"header\">
	<a href=\"{$homepage}\"><img class=\"header_img\" src=\"".WEB_PAGE_TO_ROOT."core/theming/images/logo.png\" alt=\"ice\" /></a>
	<div id=\"quote\">
	{$quote}
	</div>

	<div id=\"primary_menu\">
	{$primaryMenuHtml}
	</div>
	</div>
	<div id=\"wrapper\">
	<div id=\"container\" class=\"rounded-corners\">";
	if ($right != "") {
		echo "
			<div id=\"main_menu\">
			<div id=\"main_menu_padded\">
	{$right}
	</div>
	</div>
	<div id=\"main_body\" class=\"rounded-corners-left\">";
	} else {
		echo "
			<div id=\"main_body\" style=\"width: 100%\" class=\"rounded-corners-left\">";
	}
	echo "
	{$pPage['body']}
	<center>
	{$messagesHtml}
	</center>
	<br />
	{$pPage['below_msg']}
	</div>
	<div class=\"clear\">
	</div>
	</div>
	<div id=\"footer\" class=\"rounded-corners\">
	<p><i>".getSoftwareName()."</i> v".getVersion()." &copy; Cold Technologies</p>
	</div>
	</div>
	</body>
	</html>";
}

?>
