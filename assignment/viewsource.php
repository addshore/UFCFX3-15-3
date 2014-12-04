<?php
/**
 * This script can be included on any web page and will output the source of the page if requested
 */

if( array_key_exists( 'viewsource', $_GET ) ) {
	echo highlight_string( file_get_contents( $_SERVER['SCRIPT_FILENAME'] ) );
	exit();
}
