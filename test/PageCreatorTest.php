<?php
include __DIR__ . "/../src/wrappers/PageCreator.php";
use \mwRESTfulClients\wrappers\PageCreator as PageCreator;

/**
 * Unit tests for class PageCreator (A bot to create pages, using the mediawiki API)
 * 
 * @author Alvaro.Ortiz
 *
 */
class PageCreatorTest extends PHPUnit_Framework_TestCase {
	public $bot;
	private $csvPath;
	private $pageTemplate;
	
	public function setUp() {
		// Path to CSV file to import
		$this->csvPath =  __DIR__ . "/testdata/Test.csv";
		
		// Path to template used to fill newly created wiki pages
		$this->pageTemplate = __DIR__ . "/testdata/Test.tpl";
		
		// path to configuration .ini file
		$configPath =  __DIR__ . "/test.ini";
		$ini = parse_ini_file( $configPath );

		// Create a Snoopy (HTML client) instance
		require_once $ini[ 'snoopyPath' ];
		$snoopy = new Snoopy();
				
		// Create bot instance
		$this->bot = new PageCreator( $snoopy, $configPath );
	}
	
	public function testCreateBot() {
		$this->assertNotNull( $this->bot );
	}
	
	public function testLoadTemplate() {
		$template = $this->bot->loadTemplate( $this->pageTemplate );
		$this->assertNotNull( $template );
	}
			
	public function testCreatePage() {
		$this->bot->login();
		$template = $this->bot->loadTemplate( $this->pageTemplate );
		$resp = $this->bot->createPage( 'test300', $template );
		$this->assertNotNull( $resp );
		$this->assertFalse( strpos( $resp, 'permissions-errors' ) !== false );
		$this->assertFalse( strpos( $resp, 'wikiPreview' ) !== false );
		$this->assertFalse( strpos( $resp, 'nosuchaction' ) !== false );
		$this->bot->logout();
	}
	
}