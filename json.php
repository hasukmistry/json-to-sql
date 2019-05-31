<?php

/**
 * Autoload vendor to use packages.
 */
require_once __DIR__ . '/vendor/autoload.php'; // change path as needed.

// using classes.
require_once __DIR__ . '/classes/class-unserialize.php';

include_once __DIR__ . '/inc/config.inc.php';

$ext = new ClassUnserialize();

try {
	// creates mysql connection with environment variables.
	$conn = get_mysql_connection();

	// Unique identifier to isolate records.
	$mapping_id = '59';

	// reads data from database and add it inside array.
	$json = [];
	$json = $ext->read_data( $mapping_id, $conn );

	// closes mysql connection.
	close_mysql_connection( $conn );

	// outputs json from database.
	header( 'Content-Type: application/json' );
	echo json_encode( $json );

} catch ( Exception $e ) {
	printf( '%s', $e->getMessage() );
}
