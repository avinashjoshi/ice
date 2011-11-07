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

/*
$create_tb = "CREATE TABLE users (user_id int(6) AUTO_INCREMENT,first_name varchar(15),last_name varchar(15), user varchar(20), password varchar(32), sec_key varchar(255) NOT NULL, follow text NOT NULL, isadmin int(1) DEFAULT '0' NOT NULL, avatar varchar(70), PRIMARY KEY (user_id)) ENGINE = InnoDB;";
if( !mysql_query( $create_tb ) ){
	messagePush( "Table could not be created<br />SQL: ".mysql_error() );
	pageReload();
}

messagePush( "'users' table was created." );

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
pageReload();
