<?php

/**
 * Core class file to get response from url.
*/
if ( ! class_exists( 'ClassUrl' ) ) :
	/**
	 * Class defination for reading response from given url using guzzle.
	 */
	class ClassUrl {
		/**
		 * This variable will have the value for user agent.
		 * Can be used in request headers or response headers.
		 *
		 * @var string
		 */
		private $USER_AGENT_FIREFOX = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:64.0) Gecko/20100101 Firefox/64.0';

		/**
		 * This variable will have the value for user agent.
		 * Can be used in request headers or response headers.
		 *
		 * @var string
		 */
		private $USER_AGENT_CHROME = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36';

		/**
		 * Default constructor
		 */
		public function __construct() {}

		/**
		 * This function will get user agent.
		 *
		 * @return String
		 */
		public function get_user_agent() {
			// by default lets return firefox as user agent.
			return $this->USER_AGENT_FIREFOX;
		}

		/**
		 * This function will retrieve response from given url.
		 *
		 * @param String $url to make request.
		 * @param String $host to attach with request headers.
		 * @return Array
		 */
		public function get_response( $url, $host = '' ) {
			$response = [];

			if ( empty( $url ) ) {
				$response['error']     = true;
				$response['error_msg'] = 'No url is given.';
				return $response;
			}

			$headers = [
				'Accept'          => '*/*',
				'Cache-Control'   => 'no-cache',
				'Connection'      => 'keep-alive',
				'Host'            => $host,
				'User-Agent'      => $this->get_user_agent(),
				'accept-encoding' => 'gzip, deflate',
				'cache-control'   => 'no-cache',
			];

			try {
				$client   = new \GuzzleHttp\Client();

				$res = $client->request( 'GET', $url, [
					'verify'          => true,
					'timeout'         => 30,
					'headers'         => $headers,
					'http_errors'     => false,
					'curl'            => [
						CURLOPT_SSL_VERIFYPEER => false,
						CURLOPT_SSL_VERIFYHOST => false,
					],
				]);

				if ( 200 === $res->getStatusCode() ) {
					$response['success'] = true;
					$response['body']    = json_decode( $res->getBody()->__toString() );
				}
			} catch ( Exception $e ) {
				$response['error']     = true;
				$response['error_msg'] = $e->getMessage();
			}

			return $response;
		}
	}
endif;
