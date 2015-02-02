<?php
include __DIR__ . "/../src/wrappers/AbstractWrapper.php";
use mwRESTfulClients\wrappers\AbstractWrapper as AbstractWrapper;

/**
 * A dummy class to for testing AbstractRESTwrapper (which is abstract, so cannot be instantiated directly).
 * 
 * @author Alvaro.Ortiz
 *
 */
class DummyWrapper extends AbstractWrapper {
	/**
	 * Constructor
	 *
	 * @param Snoopy $snoopy Class to simulate a web browser
	 * @param String $configPath path to configuration .ini file
	 */
	public function __construct( $snoopy, $configPath ) {
		$this->init( $snoopy, $configPath );
	}
}
