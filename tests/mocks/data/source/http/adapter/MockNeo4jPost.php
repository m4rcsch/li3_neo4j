<?php
/**
 * li3_neo4j: a lithium database Adapter for the neo4j Graph Database
 * Derived from Lithiums MockCouchPosts.php
 *
 * @see           lithium\tests\\mocks\data\source\http\adapter\MockCouchPost.php
 * @copyright     Copyright 2012, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_neo4j\tests\mocks\data\source\http\adapter;

class MockNeo4jPost extends \lithium\data\Model {

	protected $_meta = array(
		'source' => 'posts',
		'connection' => 'mock-neo4j-connection'
	);

	protected $_schema = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'author_id' => array('type' => 'integer'),
		'title' => array('type' => 'string', 'length' => 255),
		'body' => array('type' => 'text'),
		'created' => array('type' => 'datetime'),
		'updated' => array('type' => 'datetime')
	);
}

?>