<?php
include __DIR__ . "/../src/wrappers/CSVImporter.php";
use \mwRESTfulClients\wrappers\CSVImporter as CSVImporter;

/**
 * Unit tests for class CSVImporter (a script to import a csv into a wiki table, using the mediawiki API)
 * 
 * @author Alvaro.Ortiz
 *
 */
class CSVImporterTest extends PHPUnit_Framework_TestCase {
	public $importer;
	private $csvPath;
	private $pageLemma;

	public function setUp() {		
		// Path to CSV file to import
		$this->csvPath =  __DIR__ . "/testdata/Test.csv";
		
		// Lemma (title) of the Wiki page to be created to store the csv
		$this->pageLemma = 'tableTestPage8';
		
		// path to configuration .ini file
		$configPath =  __DIR__ . "/test.ini";
		$ini = parse_ini_file( $configPath );

		// Create a Snoopy (HTML client) instance
		require_once $ini[ 'snoopyPath' ];
		$snoopy = new Snoopy();
		
		// Create importer instance
		$this->importer = new CSVImporter( $snoopy, $configPath );
	}
	
	public function testCreateImporter() {
		$this->assertNotNull( $this->importer );
	}
	
	/**
	 * Load a table
	 */
	public function testLoadTable() {
		$data = $this->importer->loadCSV( $this->csvPath );
		$this->assertEquals( 10, count( $data ) );
	}
	
	/**
	 * Make a wiki table
	 */
	public function testMakeTable() {
		$data = $this->importer->loadCSV( $this->csvPath );
		$table = $this->importer->makeWikiTable( $data );
		$this->assertNotNull( $table );
	}

	public function testCreateTablePage() {
		$this->importer->login();
		$data = $this->importer->loadCSV( $this->csvPath );
		$table = $this->importer->makeWikiTable( $data );
		$result = $this->importer->createPage( $this->pageLemma, $table );
		$this->assertNotNull( $result );
		$this->importer->logout();
	}

}