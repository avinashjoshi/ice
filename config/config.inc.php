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
 * DBM that will be used. MySQL is default
 * I have added this line to fix a few database
 * functionalities in functions.inc.php
 */
$DBMS = 'MySQL';
$installComplete = false;

/*
 * The Universitiy logo must be in core/theming/images
 * You can comment the line containing $univLogoImg
 * if you do not want a university logo
 */
$univLogoImg = 'utd_logo.jpg';

/*
 * Database variables
 * db_server = database server
 * db_database = database name used
 * db_user = database username
 * db_password = database password
 */
$_DBC = array();
$_DBC[ 'db_server' ] = 'localhost';
$_DBC[ 'db_database' ] = 'ice';
$_DBC[ 'db_user' ] = 'root';
$_DBC[ 'db_password' ] = 'root';

?>
