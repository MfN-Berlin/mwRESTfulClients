<?php
include __DIR__ . "/wrappers/CSVImporter.php";
use mwRESTfulClients\wrappers\CSVImporter as CSVImporter;

/**
 * Import a CSV into a mediawiki page
 * # make sure the CSV is in UTF-8, else convert it, e.g. using notepad.
 * # If the wiki page does not exist, it will be created (or re-created)
 * # If the wiki page exists, a new version will be added, but only if the data has changed.
 * 
 * The CSV data will be converted to a table and inserted in a page template before being uploaded. 
 * The script does not use the Mediwiki template system, just the format.
 * 
 * The wiki username, password and endpoint URL are set in an .ini file.
 * See bot.ini.example
 */

// Path to the CSV file to import
$csvPath =  __DIR__ . "/../test/testdata/Test.csv";

// Path to the page template
$tplPath =  __DIR__ . "/../test/testdata/Test.tpl";

// Title of the new wiki page, were the CSV will be inserted
$pageLemma = 'testpage';

// path to configuration .ini file
$configPath =  __DIR__ . "/config.ini";

/**
 * Extend CSVImporter to customize the output. 
 * See README.md for details.
 */
class MyCSVImporter extends CSVImporter {	
	/**
	 * Overwrite the makeWikiTable to create your own custom table.
	 * 
	 * (non-PHPdoc)
	 * @see \mwRESTfulClients\wrappers\CSVImporter::makeWikiTable()
	 */
	public function makeWikiTable( array $data ) {
		return parent::makeWikiTable( $data ); // Change this
	}
}

// Main
try {
	// Create a Snoopy (HTML client) instance
	$ini = parse_ini_file( $configPath );
	require_once $ini[ 'snoopyPath' ];
	$snoopy = new Snoopy();
	
	// Create importer instance
	$importer = new MyCSVImporter( $snoopy, $configPath );

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

