<?php
/**
 * Smart WP db-error.php
 *
 * @package Smart_WP_db_error_php
 * @version 1.0.0
 *
 * @copyright 2017-8 Alexandros Kozak
 * @license GPLv2 (or later)
 */

// Die silently if smart-wp-db-error.php has been accessed directly.
if ( ! defined( 'MAIL_FROM' )
	|| ! defined( 'MAIL_TO' )
	|| ! defined( 'ALERT_INTERVAL' ) ) {
	die();
}

header( 'HTTP/1.1 503 Service Temporarily Unavailable' );
header( 'Status: 503 Service Temporarily Unavailable' );
header( 'Retry-After: 600' );
$touched = false;
$lock    = __DIR__ . DIRECTORY_SEPARATOR . 'smart-wp-db-error.lock';
// Never send e-mail when db-error.php is accessed directly.
if ( defined( 'ABSPATH' ) ) {
	if ( file_exists( $lock ) ) {
		if ( time() - filectime( $lock ) > ALERT_INTERVAL ) {
			unlink( $lock );
		}
	} else {
		$headers  = 'From: ' . MAIL_FROM . "\n" .
			'X-Mailer: PHP/' . PHP_VERSION . "\n" .
			'X-Priority: 1 (High)';
		$protocol = isset( $_SERVER['HTTPS'] ) ? 'https' : 'http';
		$message  = 'Database Error on ' . $_SERVER['SERVER_NAME'] . "\n" .
			'The database error occurred when someone tried to open this page: '
			. $protocol . '://' . $_SERVER['SERVER_NAME']
			. $_SERVER['REQUEST_URI'] . "\n";
		$subject  = 'Database error at ' . $_SERVER['SERVER_NAME'];
		mail( MAIL_TO, $subject, $message, $headers );
		$touched = touch( $lock );
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
				<?php if ( true === $touched ) : ?>
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