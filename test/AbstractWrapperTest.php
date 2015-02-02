<?php
include __DIR__ . "/DummyWrapper.php";

/**
 * Unit tests for class AbstractRESTwrapperTest (A bot to create pages, using the mediawiki REST API)
 * 
 * @author Alvaro.Ortiz
 *
 */
class AbstractWrapperTest extends PHPUnit_Framework_TestCase {
	public $bot;
	
	public function setUp() {
		// path to configuration .ini file
		$configPath =  __DIR__ . "/test.ini";
		$ini = parse_ini_file( $configPath );

		// Create a Snoopy (HTML client) instance
		require_once $ini[ 'snoopyPath' ];
		$snoopy = new Snoopy();
	
		// Create wrapper instance
		$this->bot = new DummyWrapper( $snoopy, $configPath );
	}
	
	public function testLogin() {
		$resp = $this->bot->login();
		$this->assertNotNull( $resp );
		$this->assertFalse( strpos( $resp, '404' ) !== false );
		$this->assertFalse( strpos( $resp, 'errorbox' ) !== false );
		$this->bot->logout();
	}
	
	public function testFetchPublicPage() {
		$resp = $this->bot->fetchPage( '' );
		$this->assertNotNull( $resp );
		$this->assertFalse( strpos( $resp, 'permissions-errors' ) !== false );
	}
	
	public function testFetchPrivatePage() {
		$this->bot->login();
		$resp = $this->bot->fetchPage( 'Erweitert:Hauptseite' );
		$this->assertNotNull( $resp );
		$this->assertFalse( strpos( $resp, 'permissions-errors' ) !== false );
		$this->bot->logout();
	}
	
	public function testLogout() {
		$this->bot->login();
		$resp = $this->bot->fetchPage( 'Erweitert:Hauptseite' );
		$this->assertFalse( strpos( $resp, 'permissions-errors' ) !== false );
		$this->bot->logout();
		$resp = $this->bot->fetchPage( 'Erweitert:Hauptseite' );
		$this->assertTrue( strpos( $resp, 'permissions-errors' ) !== false );
	}
	
}