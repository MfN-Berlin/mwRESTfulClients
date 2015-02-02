<?php
include __DIR__ . "/wrappers/CSVImporter.php";
use \mwRESTfulClients\wrappers\CSVImporter as CSVImporter;

/**
 * See README.md for usage
 */

// Path to the CSV file to import
$csvPath =  __DIR__ . "/../test/testdata/Test.csv";

// Path to the page template
$tplPath =  __DIR__ . "/../test/testdata/Test.tpl";

// Title of the new wiki page, were the CSV will be inserted
$pageLemma = 'testpage';

// path to configuration .ini file
$configPath =  __DIR__ . "/config.ini";

// Main
try {
	// Create a Snoopy (HTML client) instance
	$ini = parse_ini_file( $configPath );
	require_once $ini[ 'snoopyPath' ];
	$snoopy = new Snoopy();

	// Create importer instance
	$importer = new CSVImporter( $snoopy, $configPath );

	// Load the CSV file
	$data = $importer->loadCSV( $csvPath );

	// Convert CSV into wikimarkup
	$table = $importer->makeWikiTable( $data );

	// Load template and merge with table
	$tpl = $importer->loadTemplate( $tplPath );
	$pageContent = $importer->insert( 'table', $table, $tpl );
	
	// Create (or update) a new page and fill it
	$importer->login();
	$result = $importer->createPage( $pageLemma, $pageContent );
	$importer->logout();

} catch( Exception $e ) {
	echo $e->getMessage();
}

