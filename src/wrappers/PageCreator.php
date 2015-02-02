<?php
namespace mwRESTfulClients\wrappers;
require_once "AbstractWrapper.php";

/**
 * Create a wiki page, using the mediawiki API
 * Pages are filled with a template file.
 */
class PageCreator extends AbstractWrapper {
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
	 * Load the page template
	 *
	 * @param String $path
	 * @return String
	 */
	public function loadTemplate( $path ) {
		$fp = fopen( $path, "r" );
		$template = fread( $fp, filesize( $path ) );
		fclose( $fp );
		return $template;
	}
	
	public function insert( $tagName, $table, $tpl ) {
		$tag = sprintf( '{{%s}}', $tagName );
		return str_replace( $tag, $table, $tpl );
	}
	
}
