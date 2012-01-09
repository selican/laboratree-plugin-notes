<?php 
/* SVN FILE: $Id$ */
/* Note Test cases generated on: 2010-12-20 14:58:44 : 1292857124*/
App::import('Model', 'Note');

class NoteTestCase extends CakeTestCase {
	var $Note = null;
	var $fixtures = array('app.perm', 'app.roles_permissions','app.helps', 'app.app_category', 'app.app_data', 'app.application', 'app.app_module', 'app.attachment', 'app.discussion', 'app.doc', 'app.docs_permission', 'app.docs_tag', 'app.docs_type_data', 'app.docs_type_field', 'app.docs_type', 'app.docs_type_row', 'app.docs_version', 'app.group', 'app.groups_address', 'app.groups_association', 'app.groups_award', 'app.groups_interest', 'app.groups_phone', 'app.groups_projects', 'app.groups_publication', 'app.groups_setting', 'app.groups_url', 'app.groups_users', 'app.inbox', 'app.inbox_hash', 'app.interest', 'app.message_archive', 'app.message', 'app.note', 'app.ontology_concept', 'app.preference', 'app.project', 'app.projects_association', 'app.projects_interest', 'app.projects_setting', 'app.projects_url', 'app.projects_users', 'app.role', 'app.setting', 'app.site_role', 'app.tag', 'app.type', 'app.url', 'app.user', 'app.users_address', 'app.users_association', 'app.users_award', 'app.users_education', 'app.users_interest', 'app.users_job', 'app.users_phone', 'app.users_preference', 'app.users_publication', 'app.users_url');

	function startTest() {
		$this->Note =& ClassRegistry::init('Note');
	}

	function testNoteInstance() {
		$this->assertTrue(is_a($this->Note, 'Note'));
	}

	function testNoteFind() {
		$this->Note->recursive = -1;
		$results = $this->Note->find('first');
		$this->assertTrue(!empty($results));

		$expected = array('Note' => array(
			'id'  => 1,
			'table_type' => 'user',
			'table_id'  => 1,
			'title'  => 'Home',
			'content'  => 'Welcome Home',
			'created'  => '2010-12-20 14:58:44',
			'modified'  => '2010-12-20 14:58:44',
			'permanent'  => 1,
			'read_only'  => 0,
		));
		$this->assertEqual($results, $expected);
	}

	function testArticles()
	{
		$table_type = 'user';
		$table_id = 1;

		$results = $this->Note->articles($table_type, $table_id);

		$expected = array(
			array(
				'Note' => array(
					'id'  => $results[0]['Note']['id'],
					'table_type' => 'user',
					'table_id'  => 1,
					'title'  => 'Home',
					'content'  => 'Welcome Home',
					'created'  => $results[0]['Note']['created'],
					'modified'  => $results[0]['Note']['modified'],
					'permanent'  => 1,
					'read_only'  => 0,
				),
				'User' => array(
					'id'  => $results[0]['User']['id'],
					'username' => 'testuser',
					'password' => $results[0]['User']['password'],
					'email' => 'testuser@example.com',
				//	'alt_email' => 'testtest@example.com',
					'prefix' => 'Mr.',
					'first_name' => 'Test',
					'last_name' => 'User',
					'name' => 'Test User',
					'suffix' => 'Esq.',
					'title' => 'Programmer',
					'description' => 'test',
					'status' => 'Test',
					'gender' => 'male',
					'age' => 50,
					'picture' => null,
					'privacy' => 'private',
					'activity' => $results[0]['User']['activity'],
					'registered' => $results[0]['User']['registered'],
					'hash' => $results[0]['User']['hash'],
					'private_hash' => $results[0]['User']['private_hash'],
					'auth_token' => $results[0]['User']['auth_token'],
					'auth_timestamp' => 1269625040,
					'confirmed' => 1,
					'changepass' => 0,
					'security_question' => 1,
					'security_answer' => 'hash',
					'language_id' => 1,
					'timezone_id' => 1,
					'ip' => '127.0.0.1',
					'admin' => 0,
					'type' => 'user',
					'vivo' => null,
				),
				'Group' => array(
					'id'  => $results[0]['Group']['id'],
					'name' => 'Private Test Group',
					'email' => 'testgrp+private@example.com',
					'description' => 'Test Group',
					'privacy' => 'private',
					'picture' => null,
					'created'  => $results[0]['Group']['created'],
				),
				'Project' => array(
					'id'  => $results[0]['Project']['id'],
					'name' => 'Private Test Project',
					'description' => 'Private Test Project',
					'privacy' => 'private',
					'picture' => null,
					'email' => 'testprj+private@example.com',
					'created'  => $results[0]['Project']['created'],
				),
			),
			array(
				'Note' => array(
					'id'  => $results[1]['Note']['id'],
					'table_type' => 'user',
					'table_id'  => 1,
					'title'  => 'Second',
					'content'  => 'Second Note',
					'created'  => $results[0]['Note']['created'],
					'modified'  => $results[0]['Note']['modified'],
					'permanent'  => 0,
					'read_only'  => 0,
				), 
				'User' => array(
					'id'  => $results[1]['User']['id'],
					'username' => 'testuser',
					'password' => $results[1]['User']['password'],
					'email' => 'testuser@example.com',
					//'alt_email' => 'testtest@example.com',
					'prefix' => 'Mr.',
					'first_name' => 'Test',
					'last_name' => 'User',
					'name' => 'Test User',
					'suffix' => 'Esq.',
					'title' => 'Programmer',
					'description' => 'test',
					'status' => 'Test',
					'gender' => 'male',
					'age' => 50,
					'picture' => null,
					'privacy' => 'private',
					'activity' => $results[1]['User']['activity'],
					'registered' => $results[1]['User']['registered'],
					'hash' => $results[1]['User']['hash'],
					'private_hash' => $results[1]['User']['private_hash'],
					'auth_token' => $results[1]['User']['auth_token'],
					'auth_timestamp' => 1269625040,
					'confirmed' => 1,
					'changepass' => 0,
					'security_question' => 1,
					'security_answer' => 'hash',
					'language_id' => 1,
					'timezone_id' => 1,
					'ip' => '127.0.0.1',
					'admin' => 0,
					'type' => 'user',
					'vivo' => null
				),
				'Group' => array(
					'id'  => $results[1]['Group']['id'],
					'name' => 'Private Test Group',
					'email' => 'testgrp+private@example.com',
					'description' => 'Test Group',
					'privacy' => 'private',
					'picture' => null,
					'created'  => $results[1]['Group']['created'],
				),
				'Project' => array(
					'id'  => $results[1]['Project']['id'],
					'name' => 'Private Test Project',
					'description' => 'Private Test Project',
					'privacy' => 'private',
					'picture' => null,
					'email' => 'testprj+private@example.com',
					'created'  => $results[1]['Project']['created'],
				),
			),
		);

		$this->assertEqual($results, $expected);
	}

	function testArticlesNullTableType()
	{
		$table_type = null;
		$table_id = 1;

		try
		{
			$results = $this->Note->articles($table_type, $table_id);
			$this->fail('InvalidArgumentException was expected.');
		}
		catch(InvalidArgumentException $e)
		{
			$this->pass();
		}
	}

	function testArticlesInvalidTableType()
	{
		$table_type = 'invalid';
		$table_id = 1;

		try
		{
			$results = $this->Note->articles($table_type, $table_id);
			$this->fail('InvalidArgumentException was expected.');
		}
		catch(InvalidArgumentException $e)
		{
			$this->pass();
		}
	}

	function testArticlesNullTableId()
	{
		$table_type = 'user';
		$table_id = null;

		try
		{
			$results = $this->Note->articles($table_type, $table_id);
			$this->fail('InvalidArgumentException was expected.');
		}
		catch(InvalidArgumentException $e)
		{
			$this->pass();
		}
	}

	function testArticlesInvalidTableId()
	{
		$table_type = 'user';
		$table_id = 'invalid';

		try
		{
			$results = $this->Note->articles($table_type, $table_id);
			$this->fail('InvalidArgumentException was expected.');
		}
		catch(InvalidArgumentException $e)
		{
			$this->pass();
		}
	}

	// TODO: Standardize date format in toNode
	function testToNode() {
		$this->Note->recursive = 1;
		$results = $this->Note->find('first');
		$node = $this->Note->toNode($results);

		$expected = array(
			'id'  => 1,
			'table_id' => 1,
			'table_type' => 'user',
			'title' => 'Home',
			'created' => '12/20/2010 2:58pm',
			'modified' => '12/20/2010 2:58pm',
			'content' => 'Welcome Home',
			'permanent' => 1,
			'group' => 'User: Test User',
		);

		$this->assertEqual($node, $expected);
	}

	function testToNodeNull() {
		try
		{
			$node = $this->Note->toNode(null);
			$this->fail('InvalidArgumentException was expected');
		}
		catch (InvalidArgumentException $e)
		{
			$this->pass();
		}
	}

	function testToNodeNotArray() {
		try
		{
			$node = $this->Note->toNode('string');
			$this->fail('InvalidArgumentException was expected');
		}
		catch (InvalidArgumentException $e)
		{
			$this->pass();
		}	
	}

	function testToNodeMissingModel() {
		try
		{
			$node = $this->Note->toNode(array('id' => 1));
			$this->fail('InvalidArgumentException was expected');
		}
		catch (InvalidArgumentException $e)
		{
			$this->pass();
		}
	}

	function testToNodeMissingKey() {
		try
		{
			$node = $this->Note->toNode(array('Note' => array('test' => 1)));
			$this->fail('InvalidArgumentException was expected');
		}
		catch (InvalidArgumentException $e)
		{
			$this->pass();
		}
	}
}
?>
