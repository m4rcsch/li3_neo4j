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

	protected $_testConfig = array(
		'database' => 'lithium-test',
		'persistent' => false,
		'scheme' => 'tcp',
		'host' => 'localhost',
		'login' => 'root',
		'password' => '',
		'port' => 80,
		'timeout' => 2,
		'socket' => 'lithium\tests\mocks\data\source\http\adapter\MockSocket'
	);

	protected $_model = 'li3_neo4j\tests\mocks\data\source\http\adapter\MockNeo4jPost';

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
		$this->assertFalse($this->db->get());
		$this->assertFalse($this->db->post());
		$this->assertFalse($this->db->put());
	}

	public function testConnect() {
		$this->db = new Neo4j($this->_testConfig);
		$result = $this->db->connect();
		$this->assertTrue($result);
	}

	public function testDisconnect() {
		$couchdb = new Neo4j($this->_testConfig);
		$result = $couchdb->connect();
		$this->assertTrue($result);

		$result = $couchdb->disconnect();
		$this->assertTrue($result);
	}
}

?>