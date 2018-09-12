<?php
namespace mwRESTfulClients\wrappers;

/**
 * Use the Mediawiki API to edit and create wiki pages.
 * 
 * @author Alvaro.Ortiz
 */
abstract class AbstractWrapper {
	protected $snoopy;
	protected $baseURL;
	protected $apiURL;
	protected $loginVars;
	protected $ini;
	
	/**
	 * Initialize the class. Call from child class
	 * # Inject the Snoopy (HTML client) instance
	 * # Set the base URL
	 */  
	protected function init( $snoopy, $configPath ) {
		// The Snoopy instance
		$this->snoopy = $snoopy;
		
		// Read the .ini file
		$this->ini = parse_ini_file( $configPath );
	}
	
	private function initLoginVars() {
		$this->baseURL = $this->ini[ 'baseURL' ];
		$this->apiURL = $this->baseURL . "/api.php";
		$this->loginVars = array();
		// PRE 1.27
            	// Login request, pre 1.27
		// $this->loginVars[ 'lgname' ] = $this->ini[ 'wpName' ];
		// $this->loginVars[ 'lgpassword' ] = $this->ini[ 'wpPassword' ];
		// $this->loginVars[ 'format' ] = 'php';
		// $this->loginVars[ 'action' ] = 'login';
            	// POST 1.27
            	// Get a login token
		$this->loginVars[ 'action' ] = 'query';
		$this->loginVars[ 'meta' ] = 'tokens';
		$this->loginVars[ 'type' ] = 'login';
		$this->loginVars[ 'format' ] = 'php';
	} 
	
	/**
	 * Login to the wiki via API
	 *
	 * @return String HTML
	 * @see http://www.mediawiki.org/wiki/User:Patrick_Nagel/Login_with_snoopy_post-1.15.3
	 */
	public function login() {
		$this->baseURL = $this->ini[ 'baseURL' ];
		$this->apiURL = $this->baseURL . "/api.php";
		$this->loginVars = array();

		// get a token
		$this->loginVars[ 'action' ] = 'query';
		$this->loginVars[ 'meta' ] = 'tokens';
		$this->loginVars[ 'type' ] = 'login';
		$this->loginVars[ 'format' ] = 'php';
		$this->snoopy->submit( $this->apiURL, $this->loginVars );
		$this->snoopy->cookies = $this->getCookieHeaders( $this->snoopy->headers );
		$response = unserialize( trim( $this->snoopy->results ) );
		$this->loginVars[ 'lgtoken' ] = $response[ 'query' ][ 'tokens' ][ 'logintoken' ];

		$this->loginVars[ 'action' ] = 'login';
		$this->loginVars[ 'lgname' ] = $this->ini[ 'wpName' ];
		$this->loginVars[ 'lgpassword' ] = $this->ini[ 'wpPassword' ];
		$this->snoopy->submit( $this->apiURL, $this->loginVars );
		return $this->snoopy->results;
	}
	
	/**
	 * Logout
	 */
	public function logout() {
		// $this->initLoginVars();
		$this->loginVars[ 'action' ] = 'logout';
		$this->snoopy->submit( $this->apiURL, $this->loginVars );
	}
	
	/**
	 * Create a wiki page, with given title and content.
	 * # If the wiki page does not exist, it will be created (or re-created)
 	 * # If the wiki page exists, a new version will be added, but only if the data has changed.
	 * Content is in wiki markup format.
	 * 
	 * @param String $title
	 * @param String $content
	 * @throws Exception
	 * @return String
	 * @throws exception when page already exists
	 */
	public function createPage( $title, $content ) {
		if ( !$title ) throw new \Exception( 'No page title.' );
		$title = rawurlencode( str_replace( " ", "_", $title) );
		
		// authenticate
		$this->login();

		// Open the page to get the edit token
		$this->loginVars = array();
		$this->loginVars[ 'action' ] = 'query';
		$this->loginVars[ 'prop' ] = 'info';
		$this->loginVars[ 'titles' ] = $title;
		$this->loginVars[ 'meta' ] = 'tokens';
		$this->loginVars[ 'format' ] = 'json';
		
		$this->snoopy->submit( $this->apiURL, $this->loginVars );
		$response =json_decode($this->snoopy->results,true);

		// get the edittoken from the first page returned. 
		// If the page does not exist, a page with index -1 is returned, with a new edittoken.
		// $page = array_shift( array_values( $response['query']['pages'] ) );
		// $token = $page['edittoken'];
		$token = $response['query']['tokens']["csrftoken"];

		// Create the page
		$this->loginVars[ 'action' ] = 'edit';
		$this->loginVars[ 'title' ] = $title;
		$this->loginVars[ 'text' ] = $content;
		$this->loginVars[ 'summary' ] = "";
		$this->loginVars[ 'sectiontitle' ] = "";
		$this->loginVars[ 'basetimestamp' ] = "";
		$this->loginVars[ 'token' ] = $token;
		
		// Submit
		$this->snoopy->submit( $this->apiURL, $this->loginVars );
		$finalResults = $this->snoopy->results;
		
		// Cleanup
		unset($this->loginVars['prop']);
		unset($this->loginVars['titles']);
		unset($this->loginVars['intoken']);
		unset($this->loginVars['title']);
		unset($this->loginVars['text']);
		unset($this->loginVars['summary']);
		unset($this->loginVars['sectiontitle']);
		unset($this->loginVars['basetimestamp']);
		unset($this->loginVars['token']);
		
		return $finalResults;
	}

	/**
	 * Read a wiki page
	 * 
	 * @param unknown $title
	 */
	public function fetchPage( $title ) {		
		// authenticate
		// $this->snoopy->submit( $this->apiURL, $this->loginVars );

		$this->login();

		// fetch page
		$this->loginVars[ 'action' ] = 'parse';
		$this->loginVars[ 'page' ] = $title;
		$URL = $this->baseURL . "/api.php";
		$this->snoopy->submit( $URL, $this->loginVars );
		return $this->snoopy->results;
	}
	
	// 
	//  PRIVATE FUNCTIONS
	//
	
	/**
	 * Parse snoopy headers
	 * 
	 * @param array $headers
	 * @return array
	 * @see http://www.mediawiki.org/wiki/User_talk:Patrick_Nagel/Login_with_snoopy_post-1.15.3
	 */
	private function getCookieHeaders( $headers ) {
		$cookies = array();
		foreach( $headers as $header ) {
			if( preg_match( "/Set-Cookie: ([^=]*)=([^;]*)/", $header, $matches ) )
				$cookies[$matches[1]] = $matches[2];
		}
		return $cookies;
	}
		
}