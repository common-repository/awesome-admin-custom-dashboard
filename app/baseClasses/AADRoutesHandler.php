<?php

namespace App\baseClasses;

use Exception;

class AADRoutesHandler extends AADBase {

	public $routes;

	public function init() {

		// Action to handle routes...
		add_action( "wp_ajax_aad_ajax_post", [ $this, 'ajaxPost' ] );
		add_action( "wp_ajax_nopriv_aad_ajax_post", [ $this, 'ajaxPost' ] );
		add_action( "wp_ajax_aad_ajax_get", [ $this, 'ajaxGet' ] );
		add_action( "wp_ajax_nopriv_aad_ajax_get", [ $this, 'ajaxGet' ] );

		// Get routes array...
		$this->routes = ( new AADRoutes )->routes();
	}

	public function ajaxPost() {


		header( 'Content-type: application/json' );

		$request = new AADRequest();

		$_REQUEST = $request->getInputs();
		$route_name=  sanitize_text_field($_REQUEST['route_name']);
		try {

			if ( strtolower( sanitize_text_field($_SERVER['REQUEST_METHOD'] )) !== 'post' ) {
				$error = 'Method is not allowed';
				throw new Exception( $error, 405 );
			}

			if ( ! isset( $route_name ) ) {
				$error = 'Route not found';
				throw new Exception( $error, 404 );
			}

			if ( ! $this->routes[ $route_name ] ) {
				$error = 'Route not found';
				throw new Exception( $error, 404 );
			} else {
				$route = $this->routes[ $route_name ];

				if ( strtolower( $route['method'] ) !== 'post' ) {
					$error = 'Method is not allowed';
					throw new Exception( $error, 405 );
				}

			}

			if (!isset($route['nonce'])) {
				$route['nonce'] = 1;
			}

			if ($route['nonce'] === 1) {
				if ( ! wp_verify_nonce( $_REQUEST['_ajax_nonce'], 'ajax_post' ) ) {
					$error = 'Invalid nonce in request';
					throw new Exception( $error, 419 );
				}
			}

			$this->call( $route );

		} catch ( Exception $e ) {

			$code    = $e->getCode();
			$message = $e->getMessage();

			header( "Status: $code $message" );

			echo json_encode( [
				'status'  => false,
				'message' => $e->getMessage()
			] );

		}
		wp_die();
	}


	public function ajaxGet() {

		header( 'Content-type: application/json' );

		$request = new AADRequest();

		$_REQUEST = $request->getInputs();

		if ( sanitize_text_field($_REQUEST) === '' ) {
			$_REQUEST = json_decode( file_get_contents( "php://input" ), true );
		}

		try {
			if ( strtolower( sanitize_text_field($_SERVER['REQUEST_METHOD']) ) !== 'get' ) {
				$error = 'Method is not allowed';
				throw new Exception( $error, 405 );
			}

			if ( ! isset( $route_name ) ) {
				$error = 'Route not found';
				throw new Exception( $error, 404 );
			}

			if ( ! $this->routes[ $route_name ] ) {
				$error = 'Route not found';
				throw new Exception( $error, 404 );
			} else {
				$route = $this->routes[ $route_name ];

				if ( strtolower( $route['method'] ) !== 'get' ) {
					$error = 'Method is not allowed';
					throw new Exception( $error, 405 );
				}
			}

			$this->call( $route );

		} catch ( Exception $e ) {

			$code    = $e->getCode();
			$message = $e->getMessage();

			header( "Status: $code $message" );

			echo json_encode( [
				'status'  => false,
				'message' => $e->getMessage()
			] );
		}
		wp_die();
	}

	public function call( $route ) {

		$cluster = explode( '@', $route['action'] );

		$controller = 'App\\controllers\\' . $cluster[0];
		$function   = $cluster[1];

		( new $controller )->$function();

		die;
	}

}