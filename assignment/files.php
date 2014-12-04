<?php

require_once( __DIR__ . '/viewsource.php' );

//Find all files..
$dir = new RecursiveDirectoryIterator( __DIR__ );
$ite = new RecursiveIteratorIterator( $dir );
$files = new RegexIterator( $ite, '/.*/', RegexIterator::GET_MATCH );
$fileList = array();
foreach ( $files as $file ) {
	$fileList = array_merge( $fileList, $file );
}

echo "<html>\n<head></head>\n<body>\n";
echo "<div><p>All source files can be found below:</p><ul>\n";

//Iterate over all files
foreach ( $fileList as $filePath ) {

	if(
	//Skip all composer stuff
	strstr( $filePath, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR ) ||
	//Skip over . and ..
	substr( $filePath , -1 ) === '.'
	) { continue; }

	//Get rid of the long path
	$filePath = str_replace( __DIR__ . '\\', '', $filePath );

	echo "<li><a href='$filePath?viewsource'>$filePath</a></li>\n";
}

echo "</ul><p>Any libraries used can be found in the composer.json file.</p></div>\n";
echo "</body></html>\n";