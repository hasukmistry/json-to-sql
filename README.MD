# About

This project will extract key value pairs from given json api, construct **mysql queries** and dumps into database. It has capability to navigate to **n level** of nesting to extract pairs.

# What makes this different or interesting?

Find out here: https://github.com/hasukmistry/format-json-to-key-value

# Prerequisite

PHP, Mysql & composer installed and configured.

Download/Clone this repo and do `composer install`

# Setting config

Under `./inc/config.inc.php `, Set environment variables here.

```
define( 'DB_HOST', 'localhost' );
define( 'DB_USER', '{DB_USER}' );
define( 'DB_PASS', '{DB_PASS}' );
define( 'DB_NAME', 'json_db' );

define( 'TABLE_NAME', 'json_data_bindings' );
```

# Quick nice explanation for serialization

Lets first make a request to url which returns json.

```
$req = new ClassUrl();

// Sample url to make request.

$result = $req->get_response( 'https://jsonplaceholder.typicode.com/users', $host = 'jsonplaceholder.typicode.com' );
```

Now, lets create an object of ClassSerialize.

```
$ext = new ClassSerialize();
```

Following code will illustrate an use for key value extraction and generating queries.

```
if ( ! empty( $result['success'] ) ) {
    // creates mysql connection with environment variables.
    $conn = get_mysql_connection();

    // Unique identifier to isolate records.
    $mapping_id = '59';

    // based on api response, generates and executes parent child queries.
    $res = $ext->get_queries( $conn, $mapping_id, $result['body'] );

    // closes mysql connection.
    close_mysql_connection( $conn );

    header( 'Cache-Control: no-cache, must-revalidate' ); // HTTP/1.1.
    header( 'Content-Type:text/plain' );

    // displays generated queries.
    echo $res;
}
```

# Quick nice explanation for unserialization

Lets first create an object of ClassUnserialize.

```
$ext = new ClassUnserialize();
```

Following code will illustrate an use for unserialization from database and construct json response.

```
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
```