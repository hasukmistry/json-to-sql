<?php

define( 'DB_HOST', 'localhost' );
define( 'DB_USER', '{DB_USER}' );
define( 'DB_PASS', '{DB_PASS}' );
define( 'DB_NAME', 'json_db' );

define( 'TABLE_NAME', 'json_data_bindings' );

/**
 * This function creates mysqli connection.
 *
 * @return Object
 */
function get_mysql_connection() {
	// mysqli object to connect to database.
	$conn = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );

	/* check connection */
	if ( mysqli_connect_errno() ) {
		printf( "Connect failed: %s\n", mysqli_connect_error() );
		die();
	}

	return $conn;
}

/**
 * This function closes given mysqli connection.
 *
 * @param Object $conn mysqli object.
 * @return void
 */
function close_mysql_connection( $conn ) {
	$conn->close();
}
