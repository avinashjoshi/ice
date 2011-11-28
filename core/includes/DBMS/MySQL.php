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
 * This file contains all of the code to setup the initial MySQL database.
 *
 */

/*
 * $_DBC is defined in /config/config.inc.php
 */
if( !@mysql_connect( $_DBC[ 'db_server' ], $_DBC[ 'db_user' ], $_DBC[ 'db_password' ] ) ) {
	messagePush( "Could not connect to the database - please check the config file." );
	pageReload();
}

// Create database
$drop_db = "DROP DATABASE IF EXISTS {$_DBC['db_database']};";
if( !@mysql_query ( $drop_db ) ) {
	messagePush( "Could not drop existing database<br />SQL: ".mysql_error() );
	pageReload();
}

$create_db = "CREATE DATABASE {$_DBC['db_database']};";

if( !@mysql_query ( $create_db ) ) {
	messagePush( "Could not create database<br />SQL: ".mysql_error() );
	pageReload();
}

messagePush( "Database has been created." );

// Create table
if( !@mysql_select_db( $_DBC[ 'db_database' ] ) ) {
	messagePush( 'Could not connect to database.' );
	pageReload();
}

$db_query = "SET foreign_key_checks = 0;";
if( !mysql_query( $db_query ) ){
	messagePush( "Table could not be created<br />SQL: ".mysql_error() );
	pageReload();
}

$table = "users";
$db_query = "CREATE TABLE `{$table}` (
	`LoginId` varchar(10) NOT NULL,
	`password` varchar(32) NOT NULL,
	`role` varchar(20) NOT NULL,
	PRIMARY KEY (`LoginId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
if( !mysql_query( $db_query ) ){
	messagePush( "Table could not be created<br />SQL: ".mysql_error() );
	pageReload();
}
messagePush( "Table `{$table}` created");

$table = "faculty";
$db_query = "CREATE TABLE `{$table}` (
	`Ssn` varchar(10) NOT NULL,
	`FName` varchar(30),
	`MInit` varchar(10),
	`LName` varchar(30),
	`LoginId` varchar(10) NOT NULL,
	`BDate` date,
	`Address` blob,
	`Sex` char(1),
	`Salary` decimal(12,2),
	`Email` varchar(50),
	`OfficeLoc` varchar(20),
	`Position`  varchar(30),
	`Phone` varchar(10),
	`DNo` int(6) NOT NULL,
	PRIMARY KEY (`Ssn`),
	FOREIGN KEY (`LoginId`) REFERENCES `users` (`LoginId`),
	FOREIGN KEY (`DNo`) REFERENCES `department` (`Dnumber`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
if( !mysql_query( $db_query ) ){
	messagePush( "Table could not be created<br />SQL: ".mysql_error() );
	pageReload();
}
messagePush( "Table `{$table}` created");

$table = "department";
$db_query = "CREATE TABLE `{$table}` (
	`DNumber` int(6) NOT NULL AUTO_INCREMENT,
	`DName` varchar(30) NOT NULL,
	`DeptHead` varchar(20) NOT NULL,
	`Location` varchar(30),
	`Phone` varchar(10),
	PRIMARY KEY(`DNumber`),
	FOREIGN KEY (`DeptHead`) REFERENCES `faculty` (`Ssn`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
if( !mysql_query( $db_query ) ){
	messagePush( "Table could not be created<br />SQL: ".mysql_error() );
	pageReload();
}
messagePush( "Table `{$table}` created");

$table = "course";
$db_query = "CREATE TABLE `{$table}` (
	`CNo` varchar(10) NOT NULL,
	`CName` varchar(50) NOT NULL,
	`CDesc` varchar(225) NOT NULL,
	`Credits` int (2),
	`DeptNo` int (6) NOT NULL,
	PRIMARY KEY (`CNo`),
	FOREIGN KEY (`DeptNo`) REFERENCES `department` (`DNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
if( !mysql_query( $db_query ) ){
	messagePush( "Table could not be created<br />SQL: ".mysql_error() );
	pageReload();
}
messagePush( "Table `{$table}` created");

$table = "section";
$db_query = "CREATE TABLE `{$table}` (
	`CRN` int(6) NOT NULL,
	`CNo` varchar(10) NOT NULL,
	`SecNo` varchar(10) NOT NULL,
	`SecName` varchar(10),
	`SemYear` varchar(20),
	`SemTime` varchar(20),
	`InstSsn` varchar(10) NOT NULL REFERENCES `faculty` (`Ssn`),
	`Comment` varchar(255),
	`totalstud` int(5) DEFAULT 0,
	PRIMARY KEY (`CRN`),
	FOREIGN KEY (`CNo`) REFERENCES `course` (`CNo`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
if( !mysql_query( $db_query ) ){
	messagePush( "Table could not be created<br />SQL: ".mysql_error() );
	pageReload();
}
messagePush( "Table `{$table}` created");

$table = "clo";
$db_query = "CREATE TABLE `{$table}` (
	`CNo` varchar(10) NOT NULL,
	`CLO_No` int (3) NOT NULL,
	`CLO` varchar (255) NOT NULL,
	PRIMARY KEY (`CNo`,`CLO_No`),
	FOREIGN KEY (`CNo`) REFERENCES `course` (`CNo`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
if( !mysql_query( $db_query ) ){
	messagePush( "Table could not be created<br />SQL: ".mysql_error() );
	pageReload();
}
messagePush( "Table `{$table}` created");

$table = "feedback";
$db_query = "CREATE TABLE `{$table}` (
	`CRN` int(6) NOT NULL REFERENCES `section` (`CRN`),
	`CNo` varchar(10) NOT NULL REFERENCES `course` (`CNo`),
	`CLO_No` int (3) NOT NULL REFERENCES `clo` (`CLO_No`),
	`Exceed` varchar (10) NOT NULL,
	`Meet` varchar (10) NOT NULL,
	`Progress` varchar (10) NOT NULL,
	`Below` varchar (10) NOT NULL,
	`Criteria` blob NOT NULL,
	PRIMARY KEY (`CRN`,`CNo`,`CLO_No`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
if( !mysql_query( $db_query ) ){
	messagePush( "Table could not be created<br />SQL: ".mysql_error() );
	pageReload();
}
messagePush( "Table `{$table}` created");

/*
 * Insert values from here!
 */
$table = "users";
$db_query = "INSERT INTO `{$table}` VALUES
	( 'root', MD5('toor'), 'admin' ),
		( 'axj107420', MD5('toor'), 'student' ),
	( 'chandra', MD5('toor'), 'faculty' ),
	( 'lkhan', MD5('toor'), 'faculty' ),
	( 'gupta', MD5('toor'), 'faculty' ),
	( 'skarrah', MD5('toor'), 'faculty' ),
	( 'ravip', MD5('toor'), 'faculty' ),
	( 'rbk', MD5('toor'), 'faculty' );";
if( !mysql_query( $db_query ) ){
	messagePush( "Table could not be created<br />SQL: ".mysql_error() );
	pageReload();
}
messagePush( "Inserted values into table `{$table}`");

$table = "department";
$db_query = "INSERT INTO `{$table}` (DName, DeptHead, Location, Phone) VALUES
	( 'Computer Science', '999887777', 'ECSS', '9876543210' ),
	( 'Computer Engineering', '999887777', 'ECSS', '9876543210' )
	;";
if( !mysql_query( $db_query ) ){
	messagePush( "Table could not be created<br />SQL: ".mysql_error() );
	pageReload();
}
messagePush( "Inserted values into table `{$table}`");

$table = "faculty";
$db_query = "INSERT INTO `{$table}` VALUES
	( '999887777', 'Gopal', '', 'Gupta', 'gupta', '1958-01-09', 'UTDallas', 'M', '40000', 'gupta@gmail.com', 'ECSS 4.907', 'Department Head', '9728834107', 1 ),
	( '123456789', 'Balaji', 'K', 'Raghavachari', 'rbk', '1965-01-09', 'UTDallas', 'M', '30000', 'rbk@gmail.com', 'ECSS 4.225', 'Associate Professor', '9728832136', 1 ),
	( '666884444', 'Ravi', '', 'Prakash', 'ravip', '1965-01-09', 'UTDallas', 'M', '30000', 'ravip@gmail.com', 'ECSS 4.225', 'Associate Professor', '9728832136', 1 ),
	( '999881111', 'Shyam', 'S', 'Karrah', 'skarrah', '1965-01-09', 'UTDallas', 'M', '35000', 'skarrah@gmail.com', 'ECSS 4.704', 'Senior Lecturer', '9728834197', 1 ),
	( '888665555', 'Latifur', 'R', 'Khan', 'lkhan', '1965-01-09', 'UTDallas', 'M', '30000', 'lkhan@gmail.com', 'ECSS 4.225', 'Associate Professor', '9728834137', 1 ),
	( '333445555', 'Ramaswamy', '', 'Chandrashekaran', 'chandra', '1955-01-09', 'UTDallas', 'M', '35000', 'chandra@gmail.com', 'ECSS 4.611', 'Professor', '9728832032', 1 )
	;";
if( !mysql_query( $db_query ) ){
	messagePush( "Table could not be created<br />SQL: ".mysql_error() );
	pageReload();
}
messagePush( "Inserted values into table `{$table}`");

$table = "course";
$db_query = "INSERT INTO `{$table}` VALUES
	( 'CS6360', 'Database Design', 'SOL Normalization', 3, 1 ),
	( 'CS6363', 'Design and Analysis of Computer Algorithms', 'Foundation, Basics of Algorithms', 3, 1 ),
	( 'CS6371', 'Advanced Programming Languages', 'Functional Programming, Lambda Calculus, Logic Programming', 3, 1 ),
	( 'CS1337', 'Computer Science I (JAVA)', ' Learning outcomes - Java Programming', 3, 1 ),
	( 'CS2305', 'Discrete Math for Computing I', ' Discrete analysis', 3, 1 ),
	( 'CS2336', 'Computer Science II - Java', 'Advanced Java Programming', 3, 1 ),
	( 'CS3305', 'Discrete Math for Computing II', 'Advanced Discrete Analysis', 3, 1 ),
	( 'CS3341', 'Prob & Stat', 'Plays major role for Network streams advanced stats', 3, 1 ),
	( 'CE4348', 'Operating Systems Concepts', 'OD', 3, 3 ),
	( 'EE6324', 'Information Security', 'Cryptography', 4, 2 )
	;";
if( !mysql_query( $db_query ) ){
	messagePush( "Table could not be created<br />SQL: ".mysql_error() );
	pageReload();
}
messagePush( "Inserted values into table `{$table}`");

$table = "section";
$db_query = "INSERT INTO `{$table}` (CRN, CNo, SecNo, SecName, SemYear, SemTime, InstSsn, Comment) VALUES
	( '81476', 'CS6371', '001', '', '2011', 'Fall', '999887777', '' ),
	( '84505', 'CS6360', '003', '', '2011', 'Fall', '123456789', '' ),
	( '84504', 'CS6360', '002', '', '2011', 'Fall', '888665555', '' ),
	( '85691', 'CS6363', '004', '', '2011', 'Fall', '333445555', '' ),
	( '21245', 'CS6363', '001', '', '2012', 'Spring', '123456789', '' ),
	( '12720', 'CE4348', '001', '', '2010', 'Fall', '666884444', '' )
	;";
if( !mysql_query( $db_query ) ){
	messagePush( "Table could not be created<br />SQL: ".mysql_error() );
	pageReload();
}
messagePush( "Inserted values into table `{$table}`");

$table = "clo";
$db_query = "INSERT INTO `{$table}` VALUES ";
$db_query .= file_get_contents(WEB_PAGE_TO_ROOT.'core/includes/DBMS/CLO.php', FILE_USE_INCLUDE_PATH);
$db_query.=";";
if( !mysql_query( $db_query ) ){
	messagePush( "Table could not be created<br />SQL: ".mysql_error() );
	pageReload();
}
messagePush( "Inserted values into table `{$table}`");

$db_query = "SET foreign_key_checks = 1;";
if( !mysql_query( $db_query ) ){
	messagePush( "Table could not be created<br />SQL: ".mysql_error() );
	pageReload();
}

/*
$baseUrl = 'http://'.$_SERVER[ 'SERVER_NAME' ].$_SERVER[ 'PHP_SELF' ];
$stripPos = strpos( $baseUrl, 'ice/setup.php' );
$baseUrl = substr( $baseUrl, 0, $stripPos ).'ice/hackable/users/';
*/

//Setup complete and successful
messagePush( "Setup successful!" );
messagePush( "<b>Please delete the \"install\" directory.</b>" );
pageReload();
?>
