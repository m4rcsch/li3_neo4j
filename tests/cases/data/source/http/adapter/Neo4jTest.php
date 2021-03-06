<?php
/**
 * li3_neo4j: a lithium database Adapter for the neo4j Graph Database
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2012, M.Schwering (http://github.com/dgAlien)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_neo4j\tests\cases\data\source\http\adapter;

use lithium\data\model\Query;
use lithium\data\Connections;
use lithium\data\entity\Document;
use li3_neo4j\data\source\http\adapter\Neo4j;

class Neo4jTest extends \lithium\test\Unit {

	public $db;

	protected $_configs = array();

	//using an active connection
	protected $_testConfig = array(
		'database' => 'lithium-test',
		//'persistent' => false,
		//'scheme' => 'tcp',
		'type' => 'http',
		'host' => 'localhost',
		//'login' => 'root',
		//'password' => '',
		'port' => 7474,
		'timeout' => 2,
		//'socket' => 'lithium\tests\mocks\data\source\http\adapter\MockSocket'
	);

	protected $_model = 'li3_neo4j\tests\mocks\data\source\http\adapter\MockNeo4jPost';

	/**
	 * Checking with curl is not a very nice idea, but connection to a non existand resource will
	 * throw an uncatchable Exception..
	 */
	public function skip() {
		extract($this->_testConfig);
		$domain = "{$type}://{$host}:{$port}";

		if(!isset($timeout) && (integer) $timeout == 0) {
			$timeout = 10;
		}

		if(!filter_var($domain, FILTER_VALIDATE_URL)) {
			$this->skipIf(true, 'URL is not Valid: ' . $domain);
		}

		//initialize curl
		$curlInit = curl_init($domain);
		curl_setopt($curlInit, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($curlInit, CURLOPT_HEADER, true);
		curl_setopt($curlInit, CURLOPT_NOBODY, true);
		curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, true);

		//get answer
		$response = curl_exec($curlInit);

		curl_close($curlInit);

		if (!$response) {
			$this->skipIf(true, 'Database Server for `Neo4j` not available');
		}

	}


	public function setUp() {
		$this->_configs = Connections::config();

		Connections::reset();
		$this->db = new Neo4j(array('socket' => false));

		Connections::config(array(
			'mock-neo4j-connection' => array('object' => &$this->db, 'adapter' => 'Neo4j')
		));

		$model = $this->_model;
		$entity = new Document(compact('model'));
		$this->query = new Query(compact('model', 'entity'));
	}

	public function tearDown() {
		Connections::reset();
		Connections::config($this->_configs);
		unset($this->query);
	}

	public function testAllMethodsNoConnection() {
		$this->assertTrue($this->db->connect());
		$this->assertTrue($this->db->disconnect());
		//$this->assertFalse($this->db->get());
		//$this->assertFalse($this->db->post());
		//$this->assertFalse($this->db->put());
	}

	public function testConnect() {
		$this->db = new Neo4j($this->_testConfig);
		$result = $this->db->connect();
		$this->assertTrue($result);
	}

	public function testDisconnect() {
		$adapter = new Neo4j($this->_testConfig);
		$result = $adapter->connect();
		$this->assertTrue($result);

		$result = $adapter->disconnect();
		$this->assertTrue($result);
	}

	public function testSources() {
		$adapter = new Neo4j($this->_testConfig);
		$result = $adapter->sources();
		$this->assertNull($result);
	}

	public function testDescribe() {
		$adapter = new Neo4j($this->_testConfig);
		$this->expectException('Database `companies` is not available.');
		$result = $adapter->describe('companies');

		//var_dump($result);
		$this->assertNull($result);
	}
}

?>