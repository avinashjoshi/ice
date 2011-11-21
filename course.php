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

define( 'WEB_PAGE_TO_ROOT', '' );
require_once WEB_PAGE_TO_ROOT.'core/includes/functions.inc.php';

pageStartup( array( 'authenticated', 'faculty' ) );

$course = isset ( $_GET['course'] ) ? $_GET['course'] : "";
$mode = isset ( $_GET['mode'] ) ? $_GET['mode'] : "";


$page = pageNewGrab();
$page[ 'title' ] .= $page[ 'title_separator' ].'Course List & CLO';
$page[ 'page_id' ] = 'courseclo';
databaseConnect();

$htmlMsg = "";
$loginId = currentUser();

$iconDir = WEB_PAGE_TO_ROOT.'core/theming/images/icons/';
$courseList = "";
$heading = "Courses & CLO's";
$heading .= "
	<div class=\"join\" style=\"float: right;\">
	<a href=\"course.php?mode=add\"><input value=\"Add Course\" type=\"submit\"></a>
	</div>";

function addCLONumber () {
	global $link;
	global $htmlMsg;
	global $course;
	global $number;
	$htmlMsg .= "<form action=\"{$link}\" method=\"GET\" name=\"form\">";
	$htmlMsg .= "<input type=\"hidden\" name=\"course\" value=\"{$course}\" />";
	$htmlMsg .= "<input type=\"hidden\" name=\"mode\" value=\"add\" />";
	$htmlMsg .= '<table style="width: 260px" id="mytable" cellspacing="0" summary="Comments" align="center">';
	$htmlMsg .= '<tr>';
	$htmlMsg .= '<td style="border-top: 1px solid #C1DAD7;" width=180>Number of CLO\'s to be added:</td>';
	$htmlMsg .= '<td style="padding-top: 15px; border-top: 1px solid #C1DAD7;" width="50"><input type="text" class="inputBox" style="width: 25px;" maxlength="2" value="'. $number .'" name="row" /></td>';
	$htmlMsg .= '</tr>';
	$htmlMsg .= '</table>';
	$htmlMsg .= "
		<br />
		<div class=\"join\">
		<input class=\"button\" type=\"submit\" value=\"Add\">
		<a href=\"course.php\"><input value=\"Back\" type=\"submit\"></a>
		</div>
		";
	$htmlMsg .= "</form>";
	return $htmlMsg;
}

/*
 * Pre check for Depat Head or not
 */
if ( ($mode == "add" || $mode == "edit") && !isDeptHead() ) {
	messagePush ( "You are not allowed to add/edit" );
	redirectPage ( 'home.php' );
}

if ( $mode == "add" ) {
	/*
	 * Just adding a course no CLO's
	 */
	$page[ 'page_id' ] = 'addcourse';
	if ( $course == "" ) {
		$cno = isset ( $_POST['cno'] ) ? $_POST['cno'] : "";
		$cname = isset ( $_POST['cname'] ) ? $_POST['cname'] : "";
		$cdesc = isset ( $_POST['cdesc'] ) ? $_POST['cdesc'] : "";
		$ccredits = isset ( $_POST['ccredits'] ) ? $_POST['ccredits'] : "";
		$dept = isset ( $_POST['dept'] ) ? $_POST['dept'] : "";
		if ( isset ( $_POST['AddCourse'] ) ) {
			$qry = "SELECT * FROM course WHERE CNo='{$cno}'";
			$result = @mysql_query ( $qry );
			if ( !is_numeric ( $ccredits ) ) {
				messagePush ( "Credits must be a number" );
				$ccredits = "";
			} else if ( $result && mysql_num_rows ( $result ) != 0 ) {
				messagePush ( "This course already exists!" );
			} else {
				/*
				 * Now Inserting into table course
				 */
				$qry = "INSERT INTO course values ('{$cno}', '{$cname}', '{$cdesc}', {$ccredits}, {$dept});";
				$result = @mysql_query ( $qry ) or die (mysql_error());
				if ( !$result ) {
					messagePush ( "Oops! Something went wrong" );
				} else {
					messagePush ( "Course {$cno} inserted!" );
					redirectPage ( 'course.php' );
				}
			}
		}
		$qry = "SELECT * FROM department;";
		$result = @mysql_query ( $qry );
		$link = "";
		$heading = "";
		$htmlMsg .= "<form action=\"{$link}\" method=\"POST\" name=\"formcourse\">";
		$htmlMsg .= '<table style="width: 100%" id="mytable" cellspacing="0" summary="Comments" align="center">';
		$htmlMsg .= '<tr><th colspan="2">Adding Course</th></tr>';
		$htmlMsg .= '<tr>';
		$htmlMsg .= '<th class="spec" scope="row"><center>Course ID</center></th>';
		$htmlMsg .= '<td><input type="text" class="inputBox" maxlength="6" name="cno" value="'. $cno .'" /></td>';
		$htmlMsg .= '</tr>';
		$htmlMsg .= '<tr>';
		$htmlMsg .= '<th class="spec" scope="col"><center>Course Name</center></th>';
		$htmlMsg .= '<td><input type="text" class="inputBox" maxlength="6" name="cname" value="'. $cname .'" /></td>';
		$htmlMsg .= '</tr>';
		$htmlMsg .= '<tr>';
		$htmlMsg .= '<th class="spec" scope="row"><center>Course Description</center></th>';
		$htmlMsg .= '<td><textarea  cols="60" rows="4" class="inputBox" maxlength="255" name="cdesc">'. $cdesc .'</textarea></td>';
		$htmlMsg .= '</tr>';
		$htmlMsg .= '<tr>';
		$htmlMsg .= '<th class="spec" scope="col"><center>Credits</center></th>';
		$htmlMsg .= '<td><input type="text" class="inputBox" maxlength="1" name="ccredits" value="'. $ccredits .'" /></td>';
		$htmlMsg .= '</tr>';
		$htmlMsg .= '<tr>';
		$htmlMsg .= '<th class="spec" scope="row"><center>Department</center></th>';
		$htmlMsg .= '<td> <select name="dept">';
		while ( $result && $row = mysql_fetch_assoc ( $result ) ) {
			$htmlMsg .= '<option value="'. $row['DNumber'] .'">'. $row['DName'] .'</option>';
		}
		$htmlMsg .= '</select> </td>';
		$htmlMsg .= '</tr>';
		$htmlMsg .= "</table><br />";
		$htmlMsg .= "
			<br />
			<div class=\"join\">
			<input class=\"button\" type=\"submit\" value=\"Add Course\" name=\"AddCourse\">
			<a href=\"course.php\"><input value=\"Back\" type=\"submit\"></a>
			</div>
			";
		$htmlMsg .= "</form>";
	} else {
		$number = isset ( $_GET['row'] ) ? $_GET['row'] : "";
		$page[ 'page_id' ] = 'courseclo';
		$heading = "Adding CLO for {$course}";
		$link = "";
		if ( isset ($_GET['row']) && $number == "" ) {
			messagePush ( "You must have the number of CLO's specified" );
			$htmlMsg = addCLONumber();
		} else {
			if ( isset ($_GET['row']) && !is_numeric ( $number ) ) {
				messagePush ( "Number of CLO's must be a number!" );
				$number = "";
				$htmlMsg = addCLONumber();
			} else if ( !isset ($_GET['row'])) {
				$htmlMsg = addCLONumber();
			} else {
				if ( isset ( $_POST['AddCLO'] ) ) {
					$flag = false;
					$count = isset ( $_POST['count'] ) ? $_POST['count'] : "";
					$total = isset ( $_POST['total'] ) ? $_POST['total'] : "";
					$i = $count;
					$value = array();
					while ( $i <= $total ) {
						$value[$i] = isset ( $_POST[$i] ) ? $_POST[$i] : "";
						if ( $value[$i] == "" )
							$flag = true;
						$i = $i + 1;
					}
					if ( $flag == true ) {
						messagePush ( "Enter all CLO's" );
					} else if ( $count == "" || $total == "" ) {
						messagePush ( "Oops! Looks like something went wrong!" );
						redirectPage ( "course.php?course={$course}&mode=add" );
					} else {
						$qry = "START TRANSACTION;";
						$result = @mysql_query ( $qry ) or die ( mysql_error() );
						while ( $count <= $total ) {
							$qry = "INSERT INTO clo VALUES ('{$course}', {$count}, '{$_POST[$count]}')";
							$result = @mysql_query ( $qry );
							if ( !$result ) {
								messagePush ( "Oops! something went wrong!" );
								$qry = "ROLLBACK";
								$result = @mysql_query ( $qry );
								redirectPage ( "course.php" );
							}
							$count = $count + 1;
						}
						$qry = "COMMIT";
						$result = @mysql_query ( $qry );
						messagePush ( "Inserted the CLO's");
						redirectPage ( "course.php?course={$course}&mode=view" );
					}
				}
				$qry = "SELECT * FROM clo WHERE CNo='{$course}';";
				$result = @mysql_query ( $qry );
				if ( $result && mysql_num_rows ( $result ) == 0 ) {
					messagePush ( "Course {$course} does not exist!" );
					redirectPage ( 'course.php' );
				}
				$qry = "SELECT * FROM clo WHERE CNo='{$course}';";
				$result_old = @mysql_query ( $qry );
				$qry = "SELECT COUNT(*) AS count FROM clo WHERE CNo='{$course}';";
				$result = @mysql_query ( $qry );
				$row = mysql_fetch_assoc ( $result );
				$i = $row['count'] + 1;
				$number = $number + $row['count'];
				$link = "";
				$htmlMsg .= "<form action=\"{$link}\" method=\"POST\" name=\"form\">";
				$htmlMsg .= "<input type=\"hidden\" name=\"count\" value=\"{$i}\">";
				$htmlMsg .= "<input type=\"hidden\" name=\"total\" value=\"{$number}\">";
				$htmlMsg .= '<table style="width: 100%" id="mytable" cellspacing="0" summary="Comments" align="center">';
				$htmlMsg .= '<tr><th width=10><center>CLO. No.</center></th><th width=100><center>CLO</center></th></tr>';
				while ( $result_old && $row_old = mysql_fetch_assoc ( $result_old ) ) {
					$htmlMsg .= '<tr>';
					$htmlMsg .= '<td align="center">' . $row_old['CLO_No'] . '</td>';
					$htmlMsg .= '<td>' . $row_old['CLO'] . '</td>';
					$htmlMsg .= '</tr>';
				}
				while ( $i <= $number ) {
					$htmlMsg .= "<tr>";
					$htmlMsg .= "<td><center>{$i}</center></td>";
					$htmlMsg .= '<td><input type="text" class="inputBox" maxlength="255" name="'. $i .'" value=""></td>';
					$htmlMsg .= "</tr>";
					$i = $i + 1;
				}
				$htmlMsg .= '</table>';
				$htmlMsg .= "
					<br />
					<div class=\"join\">
					<input class=\"button\" type=\"submit\" value=\"Add CLO\" name=\"AddCLO\">
					<a href=\"course.php\"><input value=\"Back\" type=\"submit\"></a>
					</div>
					";
				$htmlMsg .= "</form>";
			}
		}
	}
} else if ( isset ( $_POST['SubmitChng'] ) ) {
	$postCourse = isset ( $_POST['course'] ) ? $_POST['course'] : "";
	if ( $postCourse == "" ) {
		messagePush ( "Oops Looks like something went wrong!" );
		redirectPage( 'course.php' );
	}
	$count = isset ( $_POST['count'] ) ? $_POST['count'] : "";
	while ( $count ) {
		$qry = "UPDATE clo SET CLO='{$_POST[$count]}' WHERE CLO_No={$count} AND CNo='{$postCourse}';";
		$result = @mysql_query ( $qry ) or die ( mysql_error() );
		$count = $count - 1;
	}
	messagePush ( "Updated Successfully!" );
	redirectPage ('course.php?course='.$postCourse.'&mode=view');
} else if ( $mode != "add" && $mode != "view" && $mode != "edit" && $mode != "" ) {
	messagePush ( "Invalid mode" );
	redirectPage( 'course.php' );
} else if ( $mode == "view" ) {
	if ( $course == "" ) {
		messagePush ( "You must enter a course Number!" );
		redirectPage( 'course.php' );
	}

	$qry = "SELECT * FROM course WHERE CNo = '{$course}';";
	$result = @mysql_query ( $qry ) or die ( mysql_error() );
	if ( $result && mysql_num_rows ( $result ) != 1 ) {
		messagePush ( "Wrong course number!" );
		redirectPage ( "course.php" );
	}
	$row = mysql_fetch_assoc ( $result );
	$heading = "{$row['CName']} ({$row['CNo']})";

	$qry = "SELECT * FROM clo WHERE CNo = '{$course}';";
	$result = @mysql_query ( $qry ) or die ( mysql_error() );
	if ( $result && mysql_num_rows ( $result ) >= 1 ) {
		$courseList .= '<table style="width: 100%" id="mytable" cellspacing="0" summary="Comments" align="center">';
		$courseList .= '<tr><th align="center" width="100px">CLO Number</th><th><center>CLO Description</center></th></tr>';
		while ( $row = mysql_fetch_assoc ( $result ) ) {
			$courseList .= '<tr>';
			$courseList .= '<td align="center">' . $row['CLO_No'] . '</td>';
			$courseList .= '<td>' . $row['CLO'] . '</td>';
			$courseList .= '</tr>';
		}
		$courseList .= "</table>";
	}
	$courseList .= "
		<br />
		<div class=\"join\">
		<a href=\"course.php?course={$course}&mode=edit\"><input value=\"Edit\" type=\"submit\"></a>
		<a href=\"course.php\"><input value=\"Back\" type=\"submit\"></a>
		</div>";
} else if ( $mode == "edit" ) {
	if ( $course == "" ) {
		messagePush ( "You must enter a course Number!" );
		redirectPage( 'course.php' );
	}

	$qry = "SELECT * FROM course WHERE CNo = '{$course}';";
	$result = @mysql_query ( $qry ) or die ( mysql_error() );
	if ( $result && mysql_num_rows ( $result ) != 1 ) {
		messagePush ( "Wrong course number!" );
		redirectPage ( "course.php" );
	}
	$row = mysql_fetch_assoc ( $result );
	$heading = "{$row['CName']} ({$row['CNo']})";

	$link = WEB_PAGE_TO_ROOT.'course.php';

	$courseList .= "<form action=\"{$link}\" method=\"post\" name=\"form\">";

	$count = 0;
	$qry = "SELECT * FROM clo WHERE CNo = '{$course}';";
	$result = @mysql_query ( $qry ) or die ( mysql_error() );
	if ( $result && mysql_num_rows ( $result ) >= 1 ) {
		$courseList .= '<table style="width: 100%" id="mytable" cellspacing="0" summary="Comments" align="center">';
		$courseList .= '<tr><th align="center" width="100px">CLO Number</th><th><center>CLO Description</center></th></tr>';
		while ( $row = mysql_fetch_assoc ( $result ) ) {
			$count = $count +1;
			$courseList .= '<tr>';
			$courseList .= '<td align="center">' . $row['CLO_No'] . '</td>';
			$courseList .= '<td><input type="text" class="inputBox" maxlength="250" name="'. $row['CLO_No'] .'" value="'. $row['CLO']  .'"></td>';
			$courseList .= '</tr>';
		}
		$courseList .= "</table>";
	}
	$courseList .= "
		<br />
		<div class=\"join\">
		<input class=\"button\" type=\"submit\" value=\"Submit Changes\" name=\"SubmitChng\">
		<a href=\"course.php\"><input value=\"Back\" type=\"submit\"></a>
		<input type=\"hidden\" name=\"count\" value=\"{$count}\" />
		<input type=\"hidden\" name=\"course\" value=\"{$course}\" />
		</div>
		";
	$courseList .= "</form>";
} else if ( $mode != "add" ) {
	$qry = "SELECT * FROM course;";
	$result = @mysql_query ( $qry ) or die ( mysql_error() );
	if ( $result && mysql_num_rows ( $result ) >= 1 ) {
		$courseList .= '<table style="width: 100%" id="mytable" cellspacing="0" summary="Comments" align="center">';
		$courseList .= '<tr><th align="center" width="80px">Course ID</th><th>Course Name</th><th colspan="3"><center>CLO Options</center></th></tr>';
		while ( $row = mysql_fetch_assoc ( $result ) ) {
			$link = WEB_PAGE_TO_ROOT.'course.php?course='.$row['CNo'];
			$courseList .= '<tr>';
			$courseList .= '<td align="center">' . $row['CNo'] . '</td>';
			$courseList .= '<td>' . $row['CName'] . '</td>';
			$courseList .= '<td align="center" width="30"><a href="'. $link .'&mode=view"><img width="20px" src="'. $iconDir .'view.png" title="View CLO\'s" alt="View CLO\'s"/></a></td>';
			$courseList .= '<td align="center" width="30"><a href="'. $link .'&mode=edit"><img width="20px" src="'. $iconDir .'edit.png" title="Edit CLO\'s" alt="Add / Edit CLO\'s"/></a></td>';
			$courseList .= '<td align="center" width="30"><a href="'. $link .'&mode=add"><img width="20px" src="'. $iconDir .'add.png" title="Add CLO(s)" alt="Add / Edit CLO\'s"/></a></td>';
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
