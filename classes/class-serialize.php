<?php


/**
 * Core class file to extract response and outputs queries to it in database.
*/
if ( ! class_exists( 'ClassSerialize' ) ) :
	/**
	 * Class defination for extracting response.
	 */
	class ClassSerialize {
		/**
		 * Table name to save data.
		 *
		 * @var string
		 */
		private $table = TABLE_NAME;

		/**
		 * Default constructor
		 */
		public function __construct() {}

		/**
		 * Navigates and generate sql queries.
		 * Navigates to N level to extract all the key value pairs.
		 *
		 * @param Object  $conn mysqli object.
		 * @param LongInt $mapping_id to save key value pairs and associate.
		 * @param Array   $item containing key value pairs.
		 * @param Int     $parent for nesting key value pairs.
		 * @param Array   $queries to save sql queries.
		 * @return Array
		 */
		public function generate_insert_queries( &$conn, $mapping_id, $item, $parent = 0, &$queries = [] ) {
			foreach ( $item as $key => $value ) {
				$sql = "INSERT INTO $this->table(`mapping_id`, `api_key`, `api_response`, `is_object`, `is_array`, `data_type` ,`parent`, `created`)";

				if ( is_object( $value ) && $value instanceof stdClass ) {

					$sql .= " VALUES ( $mapping_id, '$key', '', 1, 0, '', $parent, now())";

					// Adds query into an array.
					$queries[] = $sql;

					if ( $conn->query( $sql ) ) {
						$this->generate_insert_queries( $conn, $mapping_id, $value, $conn->insert_id, $queries );
					}
				} elseif ( is_array( $value ) ) {

					$sql .= " VALUES ( $mapping_id, '$key', '', 0, 1, '', $parent, now())";

					// Adds query into an array.
					$queries[] = $sql;

					if ( $conn->query( $sql ) ) {
						$this->generate_insert_queries( $conn, $mapping_id, $value, $conn->insert_id, $queries );
					}
				} else {
					$data_type = 'text';
					if ( is_numeric( $value ) ) {
						$data_type = 'number';
					} elseif ( is_bool( $value ) === true ) {
						$data_type = 'boolean';
					} elseif ( is_null( $value ) ) {
						$data_type = 'null';
					}

					$value = json_encode( $conn->real_escape_string( $value ) );

					$sql .= " VALUES ( $mapping_id, '$key', '$value', 0, 0, '$data_type', $parent, now())";

					// Adds query into an array.
					$queries[] = $sql;

					$conn->query( $sql );
				}
			}

			return $queries;
		}

		/**
		 * Generates sql queries with key value combination.
		 *
		 * @param Object  $conn mysqli object.
		 * @param LongInt $mapping_id to save key value pairs and associate.
		 * @param Array   $item containing key value pairs.
		 * @return String
		 */
		public function get_queries( &$conn, $mapping_id, $item ) {
			$all_queries = '';
			$queries     = [];

			$create_query = "CREATE TABLE IF NOT EXISTS `$this->table` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT,`mapping_id` bigint(20) NOT NULL,`api_key` text NOT NULL,`api_response` text NOT NULL,`is_object` tinyint(1) NOT NULL DEFAULT '0',`is_array` tinyint(1) NOT NULL DEFAULT '0',`data_type` text NOT NULL,`parent` bigint(20) NOT NULL,`created` datetime NOT NULL,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$all_queries .= $create_query;

			$queries = $this->generate_insert_queries( $conn, $mapping_id, $item );

			$all_queries .= implode( ';', $queries ) . ';';

			return $all_queries;
		}
	}
endif;
