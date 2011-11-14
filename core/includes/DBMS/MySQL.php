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
 * This file contains all of the code to setup the initial MySQL database. (setup.php)
 *
 */

global $DBMS;

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
	`Ssn` varchar(20) NOT NULL,
	`FName` varchar(30),
	`MInit` varchar(10),
	`LName` varchar(30),
	`LoginId` varchar(10) NOT NULL,
	`BDate` date,
	`Address` blob,
	`Sex` char(1),
	`Salary` decimal(12,2),
	`Email` varchar(50) NOT NULL,
	`OfficeLoc` varchar(20),
	`Position`  varchar(30),
	`Phone` varchar(10),
	`Dno` int(6),
	PRIMARY KEY (`Ssn`),
	FOREIGN KEY (`LoginId`) REFERENCES `users` (`LoginId`),
	FOREIGN KEY (`Dno`) REFERENCES `Department` (`Dnumber`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
if( !mysql_query( $db_query ) ){
	messagePush( "Table could not be created<br />SQL: ".mysql_error() );
	pageReload();
}
messagePush( "Table `{$table}` created");

$table = "department";
$db_query = "CREATE TABLE `{$table}` (
	`DNumber` int(6) NOT NULL,
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
	`CourseNo` varchar(10) NOT NULL,
	`CourseName` varchar(50) NOT NULL,
	`CourseDesc` varchar(225) NOT NULL,
	`Credits` int (2),
	`DeptNo` int (6),
	PRIMARY KEY (`CourseNo`),
	FOREIGN KEY (`DeptNo`) REFERENCES `Department` (`DNumber`)
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
	( 'gupta', MD5('toor'), 'faculty' ),
	( 'rbk', MD5('toor'), 'faculty' );";
if( !mysql_query( $db_query ) ){
	messagePush( "Table could not be created<br />SQL: ".mysql_error() );
	pageReload();
}
messagePush( "Inserted values into table `{$table}`");

$table = "department";
$db_query = "INSERT INTO `{$table}` VALUES
	( 1, 'Computer Science', '999887777', 'ECSS', '9876543210' ),
	( 2, 'Computer Engineering', '999887777', 'ECSS', '9876543210' )
	;";
if( !mysql_query( $db_query ) ){
	messagePush( "Table could not be created<br />SQL: ".mysql_error() );
	pageReload();
}
messagePush( "Inserted values into table `{$table}`");

$table = "faculty";
$db_query = "INSERT INTO `{$table}` VALUES
	( '999887777', 'Gopal', '', 'Gupta', 'gupta', '1958-01-09', 'UTDallas', 'M', '40000', 'gupta@gmail.com', 'ECSS 4.907', 'Department Head', '9728834107', 1 ),
	( '123456789', 'Balaji', 'K', 'Raghavachari', 'rbk', '1965-01-09', 'UTDallas', 'M', '30000', 'rbk@gmail.com', 'ECSS 4.225', 'Professor', '9728832136', 1 ),
	( '333445555', 'Ramaswamy', '', 'Chandrashekaran', 'chandra', '1955-01-09', 'UTDallas', 'M', '35000', 'chandra@gmail.com', 'ECSS 4.611', 'Professor', '9728832032', 1 )
	;";
if( !mysql_query( $db_query ) ){
	messagePush( "Table could not be created<br />SQL: ".mysql_error() );
	pageReload();
}
messagePush( "Inserted values into table `{$table}`");

$table = "course";
$db_query = "INSERT INTO `{$table}` VALUES
	( 'CS1336', 'Programming Fundamentals', 'learning outcomes - Basics of Programming', 3, 1 ),
	( 'CS1337', 'Computer Science I (JAVA)', ' Learning outcomes - Java Programming', 3, 1 ),
	( 'CS2305', 'Discrete Math for Computing I', ' Discrete analysis', 3, 1 ),
	( 'CS2336', 'Computer Science II - Java', 'Advanced Java Programming', 3, 1 ),
	( 'CS3305', 'Discrete Math for Computing II', 'Advanced Discrete Analysis', 3, 1 ),
	( 'CS3341', 'Prob & Stat', 'Plays major role for Network streams advanced stats', 3, 1 ),
	( 'EE3345', 'Algorithm Anal. & Data Struct', 'Foundation, Basics of Algorithms', 3, 2 ),
	( 'CE4347', 'Database Systems', 'SOL Normalization', 3, 3 ),
	( 'CE4348', 'Operating Systems Concepts', 'OD', 3, 3 ),
	( 'EE6324', 'Information Security', 'Cryptography', 4, 2 )
	;";
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

$insert = "INSERT INTO users VALUES
			('1','Site','Admin','root',MD5('toor'),'dcc03fdbd17882124fdb499bb26ed29e','1','1','admin.jpg');";
if( !mysql_query( $insert ) ){
			messagePush( "Data could not be inserted into 'users' table<br />SQL: ".mysql_error() );
					pageReload();
}
messagePush( "Data inserted into 'users' table." );
 */

//Setup complete and successful
messagePush( "Setup successful!" );
messagePush( "<b>Please delete the \"install\" directory.</b>" );
pageReload();
?>
