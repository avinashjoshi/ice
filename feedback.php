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

pageStartup( array( 'authenticated', 'faculty' ) );

$mode = isset ( $_GET['mode'] ) ? $_GET['mode'] : "";
$crn = isset ( $_GET['crn'] ) ? $_GET['crn'] : "";
if ( isset ( $_POST ['crn'] ) ) {
	$crn = $_POST['crn'];
}

if ( $mode != "" && $mode != "view" && $mode != "add" ) {
	messagePush ( "Invalid mode" );
	redirectPage ( WEB_PAGE_TO_ROOT.'index.php' );
}

$page = pageNewGrab();
$page[ 'title' ] .= $page[ 'title_separator' ].'Feedback';
$page[ 'page_id' ] = 'feedback';
databaseConnect();

if ( $mode != "" || $crn != "" ) {
	$qry =  "SELECT * FROM section, course where CRN = '{$crn}' AND course.CNo=section.CNo;";
	$result = @mysql_query ( $qry ) or die ( mysql_error() );
	if ( $result && mysql_num_rows ( $result ) != 1 ) {
		messagePush ( "Oops! Looks like something went wrong!" );
		redirectPage ( "feedback.php" );
	}
	$row = mysql_fetch_assoc ( $result );
	$CNo = $row['CNo'];
	$CName = $row['CName'];
	$displayTotalStud = $row['totalstud'];
	$displayComment = $row['Comment'];
	$heading = "{$row['CName']} - {$crn}";
	$qry = "SELECT COUNT(*) AS count FROM clo WHERE CNo='{$CNo}';";
	$result = @mysql_query ( $qry ) or die ( mysql_error() );
	if ( $result && mysql_num_rows ( $result ) == 1 ) {
		$row = mysql_fetch_row ( $result );
		$count = $row[0];
		if ( $count == "0" ) {
			messagePush ( "Oops, no CLO's defined for course {$CNo} yet!" );
			redirectPage ( "feedback.php" );
		}
	}
	$flag = false;
	$totalstud = isset ( $_POST['totalstud'] ) ? $_POST['totalstud'] : "";
	$comments = isset ( $_POST['comments'] ) ? $_POST['comments'] : "";
	$insCount = $count;
	$i = $count;
	while ( $count ) {
		$value[$count] = array();
		$value[$count]['below'] = isset ( $_POST[$count.'_below'] ) ? $_POST[$count.'_below'] : "" ;
		$value[$count]['progress'] = isset ( $_POST[$count.'_progress'] ) ? $_POST[$count.'_progress'] : "" ;
		$value[$count]['meets'] = isset ( $_POST[$count.'_meets'] ) ? $_POST[$count.'_meets'] : "" ;
		$value[$count]['exceeds'] = isset ( $_POST[$count.'_exceeds'] ) ? $_POST[$count.'_exceeds'] : "" ;
		$value[$count]['criteria'] = isset ( $_POST[$count.'_criteria'] ) ? $_POST[$count.'_criteria'] : "" ;
		if ( $value[$count]['below'] == "" || $value[$count]['progress'] == "" || $value[$count]['meets'] == "" || $value[$count]['exceeds'] == "" || $value[$count]['criteria'] == "" )
			$flag = true;
		$count = $count -1;
	}
	if ( $totalstud == "" || $comments == "" )
		$flag = true;
	if ( $flag == false ) {
		while ( $i ) {
			if ( $totalstud != ( $value[$i]['below'] + $value[$i]['progress'] + $value[$i]['meets'] + $value[$i]['exceeds'] ) ) {
				$flag = true;
				messagePush ( "Sum does not match total students");
				break;
			}
			$i = $i - 1;
		}
	}
}

$link = "feedback.php?crn={$crn}";

if ( isset ( $_POST['SubmitFeed'] ) || $mode == "add" ) {
	//$qry = "SELECT * FROM feedback WHERE CRN='{$crn}' AND CLO_No IN (
	//	SELECT CLO_No FROM clo WHERE CNo='${CNo}');";
	$qry = "SELECT * FROM feedback WHERE CRN='{$crn}' AND CNo = '{$CNo}' AND CLO_No IN (
		SELECT CLO_No FROM clo WHERE CNo='{$CNo}');";
	$result = @mysql_query ( $qry ) or die ( mysql_error() );
	if ( $result && mysql_num_rows ( $result ) > 1 ) {
		messagePush ( "You have already submitted feedback!" );
		redirectPage ( $link.'&mode=view' );
	}
}
if ( isset ( $_POST['SubmitFeed'] ) ) {
	$values = array();
	if ( $flag == true ) {
		$mode = "add";
		messagePush ( "All fields required!" );
	} else {
		$i = 1;
		$qry = "START TRANSACTION;";
		$result = @mysql_query ( $qry ) or die ( mysql_error() );
		while ( $i <= $insCount ) {
			$insert_qry = "INSERT INTO feedback VALUES ({$crn}, '{$CNo}', {$i}, '{$value[$i]['exceeds']}', '{$value[$i]['meets']}', '{$value[$i]['progress']}', '{$value[$i]['below']}', '{$value[$i]['criteria']}')";
			$result = @mysql_query ( $insert_qry );
			if ( !$result ) {
				messagePush ( "Oops something went wrong");
				$qry = "ROLLBACK;";
				$result = @mysql_query ( $qry ) or die ( mysql_error() );
				redirectPage ( $link.'&mode=add' );
			}
			$i = $i + 1;
		}
		$qry = "UPDATE section SET Comment='{$comments}', totalstud={$totalstud} WHERE CRN='{$crn}'";
		$result = @mysql_query ( $qry );
		if ( !$result ) {
			messagePush ( "Oops something went wrong");
			$qry = "ROLLBACK;";
			$result = @mysql_query ( $qry ) or die ( mysql_error() );
			redirectPage ( $link.'&mode=add' );
		}
		$qry = "COMMIT;";
		$result = @mysql_query ( $qry ) or die ( mysql_error() );
		messagePush ( "Success" );
		redirectPage ( 'feedback.php' );
	}
}

$heading = "Provide Feedback";
$htmlMsg = "";
$loginId = currentUser();
$iconDir = WEB_PAGE_TO_ROOT.'core/theming/images/icons/';

$qry = "SELECT * FROM section where InstSsn IN (
	SELECT Ssn FROM faculty where LoginId = '{$loginId}');";
$result = @mysql_query($qry) or die('<pre>' . mysql_error() . '</pre>' );
if ( mysql_num_rows ( $result ) < 1 ) {
	$htmlMsg = "Oops! You have not taken any course yet!<br />";
} else if ( $crn == "" && $mode == "" ) {
	if( $result && mysql_num_rows( $result ) >= 1 ) {
		$htmlMsg = '<table style="width: 100%" id="mytable" cellspacing="0" summary="Comments" align="center">';
		$htmlMsg .= '<tr><th width=40><center>CRN</center></th><th width=30>Course</th><th width=20>Sect</th><th>Course Name</th><th>Year</th><th colspan="2"><center>Options</center></th></tr>';
		while ( $row = mysql_fetch_assoc ( $result ) ) {
			$c_qry = "SELECT * FROM course where CNo = '{$row['CNo']}';";
			$c_result = @mysql_query($c_qry) or die('<pre>' . mysql_error() . '</pre>' );
			$c_row = mysql_fetch_assoc ( $c_result );
			$link = 'feedback.php?crn='.$row['CRN'];
			$htmlMsg .= '<tr>';
			$htmlMsg .= '<td>' . $row['CRN'] . '</td>';
			$htmlMsg .= '<td>' . $row['CNo'] . '</td>';
			$htmlMsg .= '<td>' . $row['SecNo'] . '</td>';
			$htmlMsg .= '<td>' . $c_row['CName'] . '</td>';
			$htmlMsg .= '<td>' . $row['SemYear'] . ' ' . $row['SemTime'] . '</td>';
			$htmlMsg .= '<td align="center" width="30"><a href="'. $link .'&mode=view"><img width="20px" src="'. $iconDir .'view.png" title="View Feedback" alt="View CLO\'s"/></a></td>';
			$htmlMsg .= '<td align="center" width="30"><a href="'. $link .'&mode=add"><img width="20px" src="'. $iconDir .'add.png" title="Submit Feedback" alt="Add / Edit CLO\'s"/></a></td>';
			$htmlMsg .= '</tr>';
		}
		$htmlMsg .= "</Table>";
	}
} else if ( $mode == "view" ) {
	if ( $crn == "" ) {
		messagePush ( "Course Registration Number not specified" );
		redirectPage ( 'feedback.php' );
	}
	$link = 'feedback.php';
	$htmlMsg .= "<h3>{$CNo} - {$CName} - {$crn}</h3>";
	$qry = "SELECT * FROM clo WHERE CNo='{$CNo}';";
	$result = @mysql_query ( $qry ) or die ( mysql_error() );
	$clo = array();
	$i = 0;
	while ( $result && $row = mysql_fetch_assoc ( $result ) ) {
		$clo[$row['CLO_No']] = $row['CLO'];
		$i = $i + 1;
	}
	$qry = "SELECT * FROM feedback WHERE CRN='{$crn}';";
	$result = @mysql_query ( $qry ) or die ( mysql_error() );
	if ( mysql_num_rows ( $result ) < 1 ) {
		$htmlMsg = "No feedback submitted yet!";
		$htmlMsg .= "
			<br />
			<div class=\"join\">
			<a href=\"{$link}?crn={$crn}&mode=add\"><input value=\"Submit Feedback\" type=\"submit\"></a>
			<a href=\"{$link}\"><input value=\"Back\" type=\"submit\"></a>
			</div>
			";
	} else {
		$htmlMsg .= '<table style="width: 260px" id="mytable" cellspacing="0" summary="Comments" align="center">';
		$htmlMsg .= '<tr>';
		$htmlMsg .= '<td style="border-top: 1px solid #C1DAD7;" width=180>Total Number of students:</td>';
		$htmlMsg .= '<td style="border-top: 1px solid #C1DAD7;" width="50">'. $displayTotalStud .'</td>';
		$htmlMsg .= '</tr>';
		$htmlMsg .= '</table>';
		$htmlMsg .= '<br /><br />';
		$htmlMsg .= '<table style="width: 100%" id="mytable" cellspacing="0" summary="Comments" align="center">';
		$htmlMsg .= '<tr><th rowspan="2" width=10><center>Sl. No.</center></th><th rowspan = "2" width=100><center>CLO</center></th><th colspan="4"><center>Number of Students</center></th><th rowspan="2"><center>Material Used</center></th></tr>';
		$htmlMsg .= '<tr><th width="65"><center>Below Exp</center></th><th width="65"><center>Progress to Criteria</center></th><th width="65"><center>Meets Criteria</center></th><th width="65"><center>Exceeds Criteria</center></th></tr>';
		while ( $row = mysql_fetch_assoc ( $result ) ) {
			$htmlMsg .= '<tr>';
			$htmlMsg .= '<td><center>'. $row['CLO_No'] .'</center></td>';
			$htmlMsg .= '<td>'. $clo[$row['CLO_No']] .'</td>';
			$htmlMsg .= '<td><center>'. $row['Below'] .'</center></td>';
			$htmlMsg .= '<td><center>'. $row['Progress'] .'</center></td>';
			$htmlMsg .= '<td><center>'. $row['Meet'] .'</center></td>';
			$htmlMsg .= '<td><center>'. $row['Exceed'] .'</center></td>';
			$htmlMsg .= '<td><center>'. $row['Criteria'] .'</center></td>';
			$htmlMsg .= '</tr>';
		}
		$htmlMsg .= "</table><br />";
		$htmlMsg .= '<table style="width: 100%" id="mytable" cellspacing="0" summary="Comments" align="center">';
		$htmlMsg .= '<tr><th>Comments</th></tr>';
		$htmlMsg .= '<tr><td>'. $displayComment .'</td></tr>';
		$htmlMsg .= '</table>';
		$htmlMsg .= "
			<br />
			<div class=\"join\">
			<a href=\"feedback.php\"><input value=\"Back\" type=\"submit\"></a>
			</div>
			";
	}

} else if ( $mode == "add") {
	$link = 'feedback.php?crn='.$crn.'&mode=add';
	$htmlMsg .= "<h3>{$CNo} - {$CName} - {$crn}</h3>";
	$htmlMsg .= "<form action=\"{$link}\" method=\"post\" name=\"form\">";
	$htmlMsg .= "<input type=\"hidden\" name=\"crn\" value=\"{$crn}\" />";
	$htmlMsg .= '<table style="width: 260px" id="mytable" cellspacing="0" summary="Comments" align="center">';
	$htmlMsg .= '<tr>';
	$htmlMsg .= '<td style="border-top: 1px solid #C1DAD7;" width=180>Total Number of students:</td>';
	$htmlMsg .= '<td style="padding-top: 15px; border-top: 1px solid #C1DAD7;" width="50"><input type="text" class="inputBox" style="width: 25px;" maxlength="3" value="'. $totalstud .'" name="totalstud" /></td>';
	$htmlMsg .= '</tr>';
	$htmlMsg .= '</table>';
	$htmlMsg .= '<br /><br />';
	$qry = "SELECT * FROM clo WHERE CNo='{$CNo}';";
	$result = @mysql_query ( $qry ) or die ( mysql_error() );
	$htmlMsg .= '<table style="width: 100%" id="mytable" cellspacing="0" summary="Comments" align="center">';
	$htmlMsg .= '<tr><th rowspan="2" width=10><center>Sl. No.</center></th><th rowspan = "2" width=100><center>CLO</center></th><th colspan="4"><center>Number of Students</center></th><th rowspan="2"><center>Material Used</center></th></tr>';
	$htmlMsg .= '<tr><th width="65"><center>Below Exp</center></th><th width="65"><center>Progress to Criteria</center></th><th width="65"><center>Meets Criteria</center></th><th width="65"><center>Exceeds Criteria</center></th></tr>';
	while ( $row = mysql_fetch_assoc ( $result ) ) {
		$htmlMsg .= '<tr>';
		$htmlMsg .= '<td><center>'. $row['CLO_No'] .'</center></td>';
		$htmlMsg .= '<td>'. $row['CLO'] .'</td>';
		$htmlMsg .= '<td><input type="text" class="inputBoxCLO" maxlength="3" name="'. $row['CLO_No'] .'_below" value="'. $value[$row['CLO_No']]['below'] .'"></td>';
		$htmlMsg .= '<td><input type="text" class="inputBoxCLO" maxlength="3" name="'. $row['CLO_No'] .'_progress" value="'. $value[$row['CLO_No']]['progress'] .'" ></td>';
		$htmlMsg .= '<td><input type="text" class="inputBoxCLO" maxlength="3" name="'. $row['CLO_No'] .'_meets" value="'. $value[$row['CLO_No']]['meets'] .'"></td>';
		$htmlMsg .= '<td><input type="text" class="inputBoxCLO" maxlength="3" name="'. $row['CLO_No'] .'_exceeds" value="'. $value[$row['CLO_No']]['exceeds'] .'"></td>';
		$htmlMsg .= '<td><textarea class="inputBox" rows="3" cols="30" name="'. $row['CLO_No'] .'_criteria">'. $value[$row['CLO_No']]['criteria'] .'</textarea></td>';
		$htmlMsg .= '</tr>';
	}
	$htmlMsg .= "</table><br />";
	$htmlMsg .= '<table style="width: 100%" id="mytable" cellspacing="0" summary="Comments" align="center">';
	$htmlMsg .= '<tr><th>Comments</th></tr>';
	$htmlMsg .= '<tr><td><textarea class="inputBox" style="width: 97%;" rows="5" name="comments" >'. $comments .'</textarea></td></tr>';
	$htmlMsg .= '</table>';
	$htmlMsg .= "
		<br />
		<div class=\"join\">
		<input class=\"button\" type=\"submit\" value=\"Submit Feedback\" name=\"SubmitFeed\">
		<a href=\"feedback.php\"><input value=\"Back\" type=\"submit\"></a>
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
