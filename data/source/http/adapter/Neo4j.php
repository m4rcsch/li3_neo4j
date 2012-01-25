<?php
/**
 * li3_neo4j: a lithium database Adapter for the neo4j Graph Database
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2012, M.Schwering (http://github.com/dgAlien)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_neo4j\data\source\http\adapter;

use lithium\core\ConfigException;

/**
 * A data source adapter which allows you to connect to Neo4j Graph Database.
 * Atm it is not usable and just a copy of Lithiums CouchDb Class.
 *
 * By default, it will attempt to connect to the Neo4j running on `localhost` on port
 * 7474 using HTTP version 1.0.
 *
 * @link http://neo4j.org/
 */
class Neo4j extends \lithium\data\source\Http {

	/**
	 * Increment value of current result set loop
	 * used by `result` to handle rows of json responses.
	 *
	 * @var string
	 */
	protected $_iterator = 0;

	/**
	 * True if Database exists.
	 *
	 * @var boolean
	 */
	protected $_db = false;

	/**
	 * Classes used by `Neo4j`.
	 *
	 * @var array
	 */
	protected $_classes = array(
		'service' => 'lithium\net\http\Service',
		'entity' => 'lithium\data\entity\Document',
		'set' => 'lithium\data\collection\DocumentSet',
		'array' => 'lithium\data\collection\DocumentArray'
	);

	protected $_handlers = array();

	/**
	 * Constructor.
	 * @param array $config
	 */
	public function __construct(array $config = array()) {
		$defaults = array('port' => 7474, 'version' => 1);
		parent::__construct($config + $defaults);
	}

	protected function _init() {
		parent::_init();
		$this->_handlers += array(
			'integer' => function($v) { return (integer) $v; },
			'float'   => function($v) { return (float) $v; },
			'boolean' => function($v) { return (boolean) $v; }
		);
	}

	/**
	 * Ensures that the server connection is closed and resources are freed when the adapter
	 * instance is destroyed.
	 *
	 * @return void
	 */
	public function __destruct() {
		if (!$this->_isConnected) {
			return;
		}
		$this->disconnect();
		$this->_db = false;
		unset($this->connection);
	}

	/**
	 * Configures a model class by setting the primary key to `'id'`, in keeping with Neo4j
	 * conventions.
	 *
	 * @see lithium\data\Model::$_meta
	 * @see lithium\data\Model::$_classes
	 * @param string $class The fully-namespaced model class name to be configured.
	 * @return Returns an array containing keys `'classes'` and `'meta'`, which will be merged with
	 *         their respective properties in `Model`.
	 */
	public function configureClass($class) {
		return array(
			'meta' => array('key' => 'id', 'locked' => false), //@todo: think about keys in Neo4j
			'schema' => array(
				'id' => array('type' => 'string'),
				'rev' => array('type' => 'string')
			)
		);
	}

	/**
	 * Magic for passing methods to http service.
	 *
	 * @param string $method
	 * @param array $params
	 * @return void
	 */
	public function __call($method, $params = array()) {
		list($path, $data, $options) = ($params + array('/', array(), array()));
		return json_decode($this->connection->{$method}($path, $data, $options));
	}

	/**
	 * Returns an array of object types accessible through this database.
	 *
	 * @param object $class
	 * @return void
	 */
	public function sources($class = null) {
	}
}

?>