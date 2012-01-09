<?php 
/* SVN FILE: $Id$ */
/* Note Fixture generated on: 2010-12-20 14:58:44 : 1292857124*/

class NoteFixture extends CakeTestFixture {
	var $name = 'Note';
	var $table = 'notes';
	var $fields = array(
		'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'table_type' => array('type'=>'string', 'null' => false, 'default' => 'user'),
		'table_id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'title' => array('type'=>'string', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'content' => array('type'=>'text', 'null' => false, 'default' => NULL),
		'created' => array('type'=>'datetime', 'null' => false, 'default' => NULL),
		'modified' => array('type'=>'datetime', 'null' => false, 'default' => NULL),
		'permanent' => array('type'=>'boolean', 'null' => false, 'default' => '0'),
		'read_only' => array('type'=>'boolean', 'null' => false, 'default' => '0'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'table_type' => array('column' => 'table_type', 'unique' => 0), 'table_id' => array('column' => 'table_id', 'unique' => 0), 'title' => array('column' => 'title', 'unique' => 0))
	);
	var $records = array(
		array(
			'id'  => 1,
			'table_type' => 'user',
			'table_id'  => 1,
			'title'  => 'Home',
			'content'  => 'Welcome Home',
			'created'  => '2010-12-20 14:58:44',
			'modified'  => '2010-12-20 14:58:44',
			'permanent'  => 1,
			'read_only'  => 0
		),
		array(
			'id'  => 2,
			'table_type' => 'group',
			'table_id'  => 1,
			'title'  => 'Home',
			'content'  => 'Welcome Home',
			'created'  => '2010-12-20 14:58:44',
			'modified'  => '2010-12-20 14:58:44',
			'permanent'  => 1,
			'read_only'  => 0
		),
		array(
			'id'  => 3,
			'table_type' => 'project',
			'table_id'  => 1,
			'title'  => 'Home',
			'content'  => 'Welcome Home',
			'created'  => '2010-12-20 14:58:44',
			'modified'  => '2010-12-20 14:58:44',
			'permanent'  => 1,
			'read_only'  => 0
		),
		array(
			'id'  => 4,
			'table_type' => 'user',
			'table_id'  => 2,
			'title'  => 'Home',
			'content'  => 'Welcome Home',
			'created'  => '2010-12-20 14:58:44',
			'modified'  => '2010-12-20 14:58:44',
			'permanent'  => 1,
			'read_only'  => 0
		),
		array(
			'id'  => 5,
			'table_type' => 'user',
			'table_id'  => 1,
			'title'  => 'Second',
			'content'  => 'Second Note',
			'created'  => '2010-12-20 14:58:44',
			'modified'  => '2010-12-20 14:58:44',
			'permanent'  => 0,
			'read_only'  => 0
		),

	);
}
?>
