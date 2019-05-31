<?php


/**
 * Core class file to read response from database and construct json response.
*/
if ( ! class_exists( 'ClassUnserialize' ) ) :
	/**
	 * Class defination for extracting response.
	 */
	class ClassUnserialize {
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
		 * Reads data from database and construct json response.
		 *
		 * @param LongInt $mapping_id to read key value pairs and associate.
		 * @param Object  $db_con mysqli_connect object.
		 * @param LongInt $parent level to begin reading from.
		 * @return Array
		 */
		public function read_data( $mapping_id, $db_con, $parent = 0 ) {
			$json_data = [];
			$sql       = "SELECT * FROM $this->table where `mapping_id` = $mapping_id and `parent` = $parent";
			$result    = $db_con->query( $sql );

			if ( $result->num_rows > 0 ) {
				// output data of each row.
				while ( $row = $result->fetch_assoc() ) {
					if ( $row['is_object'] ) {
						$loop_data = new stdClass();
						$loop_data = (object) $this->read_data( $mapping_id, $db_con, $row['id'] );

						$json_data[ $row['api_key'] ] = $loop_data;
					} elseif ( $row['is_array'] ) {
						$json_data [ $row['api_key'] ] = $this->read_data( $mapping_id, $db_con, $row['id'] );
					} else {
						$api_response = json_decode( $row['api_response'] );

						if ( 'number' === $row['data_type'] ) {
							$json_data[ $row['api_key'] ] = $api_response + 0;
						} elseif ( 'boolean' === $row['data_type'] ) {
							$json_data[ $row['api_key'] ] = '1' === $api_response ? true : false;
						} elseif ( 'null' === $row['data_type'] ) {
							$json_data[ $row['api_key'] ] = null;
						} else {
							$json_data[ $row['api_key'] ] = $api_response;
						}
					}
				}
			}
			return $json_data;
		}
	}
endif;
