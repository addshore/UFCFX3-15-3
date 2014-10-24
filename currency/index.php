<?php

date_default_timezone_set("GMT");

//Keep stuff out of the global state
call_user_func( function () {

	//Extract params
	extract($_REQUEST);

	//Die if stuff was missing in request
	$expectedParams = array('amnt', 'from', 'to');
	$hadMissing = false;
	foreach( $expectedParams as $param ) {
		if( !isset( $$param ) ) {
			$hadMissing = true;
			echo "Missing param $param.\n";
		}
	}
	if( $hadMissing ) {
		die();
	}
	unset($hadMissing);

	//Do Stuff
	/**
	 * @var string|int $amnt
	 * @var string $from
	 * @var string $to
	 */
	mysql_connect("localhost", "root", "toor") or die(mysql_error());
	mysql_select_db("atwd_currency") or die(mysql_error());

	$r_from = mysql_query("SELECT * FROM currencies WHERE code='$from'")
	or die(mysql_error());
	$r_to = mysql_query("SELECT * FROM currencies WHERE code='$to'")
	or die(mysql_error());

	$row_from = mysql_fetch_assoc($r_from);
	$row_to = mysql_fetch_assoc($r_to);

	$rate = $row_to['rate']/$row_from['rate'];
	$calc = $rate*$amnt;

	//Output Stuff
	$data = array(
		'at' => date("d F Y H:i"),
		'rate' => $rate,
		'from' => array(
			'code' => $row_from['code'],
			'curr' => $row_from['curr'],
			'loc' => $row_from['loc'],
			'amnt' => $amnt,
		),
		'to' => array(
			'code' => $row_to['code'],
			'curr' => $row_to['curr'],
			'loc' => $row_to['loc'],
			'amnt' => number_format($calc, 2, '.', ''),
		),
	);
	header( 'Content-Type:text/xml' );
	echo convDataToXml(
		$data,
		new SimpleXMLElement( '<conv/>' )
	)->asXML();

} );

/**
 * @param array $convData to be added to the xml
 * @param SimpleXMLElement $xml base xml
 *
 * @return SimpleXMLElement
 */
function convDataToXml($data, &$xml) {
	foreach ( $data as $key => $value ) {
		if ( is_array( $value ) ) {
			if ( !is_numeric( $key ) ) {
				convDataToXml( $value, $xml->addChild( $key ) );
			}
		} else {
			$xml->addChild( $key, htmlspecialchars( $value ) );
		}
	}
	return $xml;
}