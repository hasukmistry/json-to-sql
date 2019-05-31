<?php

/**
 * Autoload vendor to use packages.
 */
require_once __DIR__ . '/vendor/autoload.php'; // change path as needed.

// using classes.
require_once __DIR__ . '/classes/class-url.php';
require_once __DIR__ . '/classes/class-serialize.php';

include_once __DIR__ . '/inc/config.inc.php';

$req = new ClassUrl();
$ext = new ClassSerialize();

try {
	// Sample url to make request.
	$result = $req->get_response( 'https://jsonplaceholder.typicode.com/users', $host = 'jsonplaceholder.typicode.com' );

	if ( ! empty( $result['success'] ) ) {
		// creates mysql connection with environment variables.
		$conn = get_mysql_connection();

		// Unique identifier to isolate records.
		$mapping_id = '59';

		// based on api response, generates parent child queries.
		$res = $ext->get_queries( $conn, $mapping_id, $result['body'] );

		// closes mysql connection.
		close_mysql_connection( $conn );

		header( 'Cache-Control: no-cache, must-revalidate' ); // HTTP/1.1.
		header( 'Content-Type:text/plain' );

		// displays generated queries.
		echo $res;
	}
} catch ( Exception $e ) {
	printf( '%s', $e->getMessage() );
}
