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

# DBM that will be used. MySQL is default
# Will try to provide support for other databases in the future

$DBMS = 'MySQL';

# Database variables
$_DBC = array();
$_DBC[ 'db_server' ] = 'localhost';
$_DBC[ 'db_database' ] = 'ice';
$_DBC[ 'db_user' ] = 'root';
$_DBC[ 'db_password' ] = 'root';

?>
