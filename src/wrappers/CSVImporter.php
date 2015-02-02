<?php
namespace mwRESTfulClients\wrappers;
require_once "PageCreator.php";

/**
 * Import a csv into a wiki table, using the mediawiki API
 */
class CSVImporter extends PageCreator {
	/**
	 * Constructor
	 *
	 * @param Snoopy $snoopy Class to simulate a web browser
	 * @param String $configPath path to configuration .ini file
	 */
	public function __construct( $snoopy, $configPath ) {
		$this->init( $snoopy, $configPath );
	}
	
	/**
	 * Load a ';' separated list of entries into an array. CSV column headers are array keys, CSV rows are values.
	 *
	 * @param String $path
	 * @return array
	 */
	public function loadCSV( $path ) {
		$header = null;
		$data = array();
		if ( ($handle = fopen( $path, "r" )) !== false ) {
			while( ( $row = fgetcsv( $handle, 2000, ';' ) ) !== false ) {
				if( !$header ) {
					$header = $row;
	
				} else {
					$data[] = array_combine( $header, $row );
				}
			}
			fclose( $handle );
		}
		return $data;
	}	
	
	/**
	 * Make a table in wiki markup. Columns are array keys, rows are array values.
	 *
	 * @param array $data
	 * @return string
	 */
	public function makeWikiTable( array $data ) {
		// open table
		$resp = '{| style="width: 500px" border="1" cellpadding="1" cellspacing="1"' . "\n";
		$resp .= '|-' . "\n";
	
		// table headers
		$headers = array_keys( $data[0] );
		foreach( $headers as $h ) {
			$resp .= '! scope="col" | ' . $h . '<br/>' . "\n";
		}
		$resp .= '|-' . "\n";
	
		// table rows
		foreach ( $data as $line ) {
			$columns = array_values( $line );
			foreach( $columns as $value ) {
				$resp .= '| ' . $value . "\n";
			}
			$resp .= '|-' . "\n";
		}
	
		// close table
		$resp .= '|}' . "\n";
		return $resp;
	}

}