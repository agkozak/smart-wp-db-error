# Smart WP db-error.php

<p align="center">
    <img src="img/mascot.png" alt="Smart WP db-error.php Mascot">
</p>

## Overview

One of the most common problems facing a WordPress webmaster is the occasional drop in database connectivity. Left to its own devices, WordPress simply displays to the end user the message

![Error establishing a database connection](img/error.png)

The webmaster has no way of knowing that an error has occurred.

WordPress allows us to address this problem in the following way: if it cannot connect to its database, it will run the drop-in plugin `/wp-content/db-error.php` ([documentation](https://developer.wordpress.org/reference/functions/dead_db/)) if it exists. Smart WP db-error.php uses that built-in functionality to serve a 503 page informing users of the outage, while e-mailing webmasters to alert them to the problem -- but only at specified intervals (default: 5 minutes), so as not to overwhelm their mail servers and inboxes.

## Installation

To install Smart WP db-error.php, execute the following:

    cd /path/to/wp-content
    git clone https://github.com/agkozak/smart-wp-db-error.git
    cd smart-wp-db-error
    cp db-error.php.dist ../db-error.php
    cd ..

At this point it is vitally necessary that you edit the new `/wp-content/db-error.php` file so as to include installation-specific information. The defaults are

    define( 'MAIL_TO', 'Firstname Lastname <example@example.com>' );
    define( 'MAIL_FROM', 'example@website.com' );
    define( 'ALERT_INTERVAL', 300 );        // In seconds.
	define( 'SUPPRESS_CREDITS', false );

`MAIL_TO` and `MAIL_FROM` should be addresses chosen to cause the least trouble for spam filters (e-mail sent by PHP from a webserver is likely to need whitelisting). `ALERT_INTERVAL` is the number of seconds between attempts at mailing the webmaster.

## Notes

If `/wp-content/db-error.php` is accessed directly, the error page will be displayed, but only for the purposes of showing the webmaster what the error page looks like. No e-mail will be sent. A `noindex` meta tag reminds search engines never to index the error page (an unlikely event anyway, as the page is served with a 503 status).

If, on the other hand, `/wp-content/smart-wp-db-error/smart-wp-db-error.php` is accessed directly, the `MAIL_TO`, `MAIL_FROM`, and `ALERT_INTERVAL` constants will not have been defined, and the script will `die` quietly.
