<?php
/**
 * Smart WP db-error.php
 *
 * @package Smart_WP_db_error_php
 * @version 1.0.6
 *
 * @copyright 2017-2018 Alexandros Kozak
 * @license GPLv2 (or later)
 */

// Die silently if smart-wp-db-error.php has been accessed directly.
if ( ! defined( 'MAIL_FROM' )
	|| ! defined( 'MAIL_TO' )
	|| ! defined( 'ALERT_INTERVAL' ) ) {
	die();
}

// Information protocol of incoming request.
if ( isset( $_SERVER['SERVER_PROTOCOL'] ) ) {
	$server_protocol = $_SERVER['SERVER_PROTOCOL'];
} else {
	$server_protocol = 'HTTP/1.1';
}

header( $server_protocol . ' 503 Service Temporarily Unavailable' );
header( 'Status: 503 Service Temporarily Unavailable' );
header( 'Retry-After: 600' );
$touched = false;
$lock    = __DIR__ . DIRECTORY_SEPARATOR . 'smart-wp-db-error.lock';
// When db-error.php is accessed directly, only show the message; do not e-mail.
if ( defined( 'ABSPATH' ) ) {

	// If lock exists and is older than the alert interval, delete it.
	if ( file_exists( $lock ) ) {
		if ( time() - filectime( $lock ) > ALERT_INTERVAL ) {
			unlink( $lock );
		}

	// Otherwise try to create the lock; if successful, send the alert e-mail.
	} elseif ( touch( $lock ) ) {
		$touched = true;
		$headers = 'From: ' . MAIL_FROM . "\n" .
			'X-Mailer: PHP/' . PHP_VERSION . "\n" .
			'X-Priority: 1 (High)';

		// Encrypted vs. non-encrypted connection.
		if ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] ) {
			$web_protocol = 'https';
		} else {
			$web_protocol = 'http';
		}

		// Server name.
		if ( isset( $_SERVER['SERVER_NAME'] ) ) {
			$server_name = filter_var( stripslashes(
				$_SERVER['SERVER_NAME']                       // Input var okay.
			), FILTER_SANITIZE_URL );
		} else {
			$server_name = '';
		}

		// Request URI.
		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			$request_uri = filter_var( stripslashes(
				$_SERVER['REQUEST_URI']                   // Input var okay.
			), FILTER_SANITIZE_URL );
		} else {
			$request_uri = '';
		}

		// The e-mail alert.
		$message = 'Database Error on ' . $server_name . "\n" .
			'The database error occurred when someone tried to open this page: '
			. $web_protocol . '://' . $server_name . $request_uri . "\n";
		$subject = 'Database error at ' . $server_name;
		mail( MAIL_TO, $subject, $message, $headers );
	}
}
?>

<!DOCTYPE HTML>
<html>
<head>
	<meta name="robots" content="noindex">
	<title>Database Error</title>
	<style>
		body {
			background-color: #5b474c;
			font-family: "Courier New", Courier, monospace;
		}

		#wrapper {
			max-width: 600px;
			margin: auto;
		}

		#error {
			padding: 5%;
			color: #000;
			background-color: #fff;
			font-size: x-large;
			text-align: center;
		}

		#error h1 {
			text-transform: uppercase;
		}

		#credits {
			padding: 10px 5% 10px 5%;
			background-color: #000;
			text-align: center;
		}

		#credits small {
			font-size: larger;
		}

		#credits a {
			color: #fff;
		}

		#credits a:hover {
			color: #e399a7;
		}
		</style>
	</head>

	<body>
		<div id="wrapper">
			<div id="error">
				<h1>Database Error</h1>
				<p>Sorry for the inconvenience.  Check back later.</p>
				<?php if ( true === $touched || ( file_exists( $lock ) && time() - filectime( $lock ) <= ALERT_INTERVAL ) ) : ?>
				<p>Administrator alerted.</p>
				<?php endif; ?>
			</div>
			<?php if ( true !== SUPPRESS_CREDITS ) : ?>
			<div id="credits">
				<small><a href="https://github.com/agkozak/smart-wp-db-error">Smart WP db-error.php</a></small>
			</div>
			<?php endif; ?>
		</div>
	</body>
</html>
<?php
// @codingStandardsIgnoreLine
// vim: ts=4:sts=4:sw=4:noet
