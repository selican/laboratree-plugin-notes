<?php
App::import('Controller','Notes');
App::import('Component', 'RequestHandler');

Mock::generatePartial('RequestHandlerComponent', 'NotesControllerMockRequestHandlerComponent', array('prefers'));

class NotesControllerTestNotesController extends NotesController {
	var $name = 'Notes';
	var $autoRender = false;

	var $redirectUrl = null;
	var $renderedAction = null;
	var $error = null;
	var $stopped = null;
	
	function redirect($url, $status = null, $exit = true)
	{
		$this->redirectUrl = $url;
	}
	function render($action = null, $layout = null, $file = null)
	{
		$this->renderedAction = $action;
	}

	function cakeError($method, $messages = array())
	{
		if(!isset($this->error))
		{
			$this->error = $method;
		}
	}
	function _stop($status = 0)
	{
		$this->stopped = $status;
	}
}

class NotesControllerTest extends CakeTestCase {
	var $Notes = null;
	var $fixtures = array('app.helps', 'app.app_category', 'app.app_data', 'app.application', 'app.app_module', 'app.attachment', 'app.digest', 'app.discussion', 'app.doc', 'app.docs_permission', 'app.docs_tag', 'app.docs_type_data', 'app.docs_type_field', 'app.docs_type', 'app.docs_type_row', 'app.docs_version', 'app.group', 'app.groups_address', 'app.groups_association', 'app.groups_award', 'app.groups_interest', 'app.groups_phone', 'app.groups_projects', 'app.groups_publication', 'app.groups_setting', 'app.groups_url', 'app.groups_users', 'app.inbox', 'app.inbox_hash', 'app.interest', 'app.message_archive', 'app.message', 'app.note', 'app.ontology_concept', 'app.preference', 'app.project', 'app.projects_association', 'app.projects_interest', 'app.projects_setting', 'app.projects_url', 'app.projects_users', 'app.role', 'app.setting', 'app.site_role', 'app.tag', 'app.type', 'app.url', 'app.user', 'app.users_address', 'app.users_association', 'app.users_award', 'app.users_education', 'app.users_interest', 'app.users_job', 'app.users_phone', 'app.users_preference', 'app.users_publication', 'app.users_url', 'app.ldap_user');
	
	function startTest() {
		$this->Notes = new NotesControllerTestNotesController();
		$this->Notes->constructClasses();
		$this->Notes->Component->initialize($this->Notes);
		
		$this->Notes->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'testuser',
			'changepass' => 0,
		));
	}

	function testNotesControllerInstance() {
		$this->assertTrue(is_a($this->Notes, 'NotesController'));
	}

	function testIndex()
	{
		$table_type = 'group';
		$table_id = 1;

		$this->Notes->params = Router::parse('notes/index/' . $table_type . '/' . $table_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->index($table_type, $table_id);
		
		$this->assertEqual($this->Notes->redirectUrl, '/notes/' . $table_type . '/' . $table_id);
	}
	
	function testIndexNullTableType()
	{
		$table_type = null;
		$table_id = 1;

		$this->Notes->params = Router::parse('notes/index/' . $table_type . '/' . $table_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->index($table_type, $table_id);
		
		$this->assertEqual($this->Notes->error, 'missing_field');
	}

	function testIndexInvalidTableType()
	{
		$table_type = 'invalid';
		$table_id = 1;

		$this->Notes->params = Router::parse('notes/index/' . $table_type . '/' . $table_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->index($table_type, $table_id);
		
		$this->assertEqual($this->Notes->error, 'invalid_field');
	}
	
	function testIndexNullTableId()
	{
		$table_type = 'group';
		$table_id = null;

		$this->Notes->params = Router::parse('notes/index/' . $table_type . '/' . $table_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->index($table_type, $table_id);
		
		$this->assertEqual($this->Notes->error, 'missing_field');
	}

	function testIndexInvalidTableId()
	{
		$table_type = 'group';
		$table_id = 'invalid';

		$this->Notes->params = Router::parse('notes/index/' . $table_type . '/' . $table_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->index($table_type, $table_id);
		
		$this->assertEqual($this->Notes->error, 'invalid_field');
	}

	function testUserHome()
	{
		$user_id = 1;

		$this->Notes->params = Router::parse('notes/user/' . $user_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->user($user_id);

		$conditions = array(
			'Note.table_type' => 'user',
			'Note.table_id' => $user_id,
			'Note.title' => 'Home',
		);
		$this->Notes->Note->recursive = -1;
		$result = $this->Notes->Note->find('first', array('conditions' => $conditions));
		$this->assertFalse(empty($result));

		$article_id = $result['Note']['id'];
		
		$this->assertEqual($this->Notes->redirectUrl, '/notes/view/' . $article_id);
	}

	function testUserArticles()
	{
		$user_id = 3;

		$this->Notes->Session->write('Auth.User', array(
			'id' => 3,
			'username' => 'thirduser',
			'changepass' => 0,
		));

		$this->Notes->params = Router::parse('notes/user/' . $user_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->user($user_id);
		
		$this->assertEqual($this->Notes->redirectUrl, '/notes/articles/user/' . $user_id);
	}
	
	function testUserNullUserId()
	{
		$user_id = null;

		$this->Notes->params = Router::parse('notes/user/' . $user_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->user($user_id);
		
		$this->assertEqual($this->Notes->error, 'missing_field');
	}

	function testUserInvalidUserId()
	{
		$user_id = 'invalid';

		$this->Notes->params = Router::parse('notes/user/' . $user_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->user($user_id);
		
		$this->assertEqual($this->Notes->error, 'invalid_field');
	}

	function testUserInvalidUserIdNotFound()
	{
		$user_id = 9000;

		$this->Notes->params = Router::parse('notes/user/' . $user_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->user($user_id);
		
		$this->assertEqual($this->Notes->error, 'invalid_field');
	}

	function testUserAccessDenied()
	{
		$user_id = 2;

		$this->Notes->params = Router::parse('notes/user/' . $user_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->user($user_id);
		
		$this->assertEqual($this->Notes->error, 'access_denied');
	}

	function testGroupHome()
	{
		$group_id = 1;

		$this->Notes->params = Router::parse('notes/group/' . $group_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->group($group_id);

		$conditions = array(
			'Note.table_type' => 'group',
			'Note.table_id' => $group_id,
			'Note.title' => 'Home',
		);
		$this->Notes->Note->recursive = -1;
		$result = $this->Notes->Note->find('first', array('conditions' => $conditions));
		$this->assertFalse(empty($result));

		$article_id = $result['Note']['id'];
		
		$this->assertEqual($this->Notes->redirectUrl, '/notes/view/' . $article_id);
	}

	function testGroupArticles()
	{
		$group_id = 3;

		$this->Notes->params = Router::parse('notes/group/' . $group_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->group($group_id);
		
		$this->assertEqual($this->Notes->redirectUrl, '/notes/articles/group/' . $group_id);
	}
	
	function testGroupNullGroupId()
	{
		$group_id = null;

		$this->Notes->params = Router::parse('notes/group/' . $group_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->group($group_id);
		
		$this->assertEqual($this->Notes->error, 'missing_field');
	}

	function testGroupInvalidGroupId()
	{
		$group_id = 'invalid';

		$this->Notes->params = Router::parse('notes/group/' . $group_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->group($group_id);
		
		$this->assertEqual($this->Notes->error, 'invalid_field');
	}

	function testGroupInvalidGroupIdNotFound()
	{
		$group_id = 9000;

		$this->Notes->params = Router::parse('notes/group/' . $group_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->group($group_id);
		
		$this->assertEqual($this->Notes->error, 'invalid_field');
	}

	function testGroupAccessDenied()
	{
		$group_id = 2;

		$this->Notes->params = Router::parse('notes/group/' . $group_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->group($group_id);
		
		$this->assertEqual($this->Notes->error, 'access_denied');
	}

	function testProjectHome()
	{
		$project_id = 1;

		$this->Notes->params = Router::parse('notes/project/' . $project_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->project($project_id);

		$conditions = array(
			'Note.table_type' => 'project',
			'Note.table_id' => $project_id,
			'Note.title' => 'Home',
		);
		$this->Notes->Note->recursive = -1;
		$result = $this->Notes->Note->find('first', array('conditions' => $conditions));
		$this->assertFalse(empty($result));

		$article_id = $result['Note']['id'];
		
		$this->assertEqual($this->Notes->redirectUrl, '/notes/view/' . $article_id);
	}

	function testProjectArticles()
	{
		$project_id = 3;

		$this->Notes->params = Router::parse('notes/project/' . $project_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->project($project_id);
		
		$this->assertEqual($this->Notes->redirectUrl, '/notes/articles/project/' . $project_id);
	}
	
	function testProjectNullProjectId()
	{
		$project_id = null;

		$this->Notes->params = Router::parse('notes/project/' . $project_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->project($project_id);
		
		$this->assertEqual($this->Notes->error, 'missing_field');
	}

	function testProjectInvalidProjectId()
	{
		$project_id = 'invalid';

		$this->Notes->params = Router::parse('notes/project/' . $project_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->project($project_id);
		
		$this->assertEqual($this->Notes->error, 'invalid_field');
	}

	function testProjectInvalidProjectIdNotFound()
	{
		$project_id = 9000;

		$this->Notes->params = Router::parse('notes/project/' . $project_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->project($project_id);
		
		$this->assertEqual($this->Notes->error, 'invalid_field');
	}

	function testProjectAccessDenied()
	{
		$project_id = 2;

		$this->Notes->params = Router::parse('notes/project/' . $project_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->project($project_id);
		
		$this->assertEqual($this->Notes->error, 'access_denied');
	}

	function testArticles()
	{
		$table_type = 'group';
		$table_id = 1;

		$this->Notes->params = Router::parse('notes/articles/' . $table_type . '/' . $table_id . '.json');
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->RequestHandler = new NotesControllerMockRequestHandlerComponent();
		$this->Notes->RequestHandler->setReturnValue('prefers', true);

		$this->Notes->articles($table_type, $table_id);

		$this->assertTrue(isset($this->Notes->viewVars['response']));
		$response = $this->Notes->viewVars['response'];
		$this->assertTrue($response['success']);

		$expected = array(
			'success' => 1,
			'articles' => array(
				array(
					'id' => $response['articles'][0]['id'],
					'table_id' => 1,
					'table_type' => 'group',
					'title' => 'Home',
					'created' => $response['articles'][0]['created'],
					'modified' => $response['articles'][0]['modified'],
					'content' => 'Welcome Home',
					'permanent' => 1,
					'group' => 'Group: Private Test Group',
				),
			),
		);
		$this->assertEqual($response, $expected);
	}

	function testArticlesNullTableType()
	{
		$table_type = null;
		$table_id = 1;

		$this->Notes->params = Router::parse('notes/articles/' . $table_type . '/' . $table_id . '.json');
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->RequestHandler = new NotesControllerMockRequestHandlerComponent();
		$this->Notes->RequestHandler->setReturnValue('prefers', true);

		$this->Notes->articles($table_type, $table_id);

		$this->assertEqual($this->Notes->error, 'missing_field');
	}

	function testArticlesInvalidTableType()
	{
		$table_type = 'invalid';
		$table_id = 1;

		$this->Notes->params = Router::parse('notes/articles/' . $table_type . '/' . $table_id . '.json');
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->RequestHandler = new NotesControllerMockRequestHandlerComponent();
		$this->Notes->RequestHandler->setReturnValue('prefers', true);

		$this->Notes->articles($table_type, $table_id);

		$this->assertEqual($this->Notes->error, 'invalid_field');
	}

	function testArticlesNullTableId()
	{
		$table_type = 'group';
		$table_id = null;

		$this->Notes->params = Router::parse('notes/articles/' . $table_type . '/' . $table_id . '.json');
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->RequestHandler = new NotesControllerMockRequestHandlerComponent();
		$this->Notes->RequestHandler->setReturnValue('prefers', true);

		$this->Notes->articles($table_type, $table_id);

		$this->assertEqual($this->Notes->error, 'missing_field');
	}

	function testArticlesInvalidTableId()
	{
		$table_type = 'group';
		$table_id = 'invalid';

		$this->Notes->params = Router::parse('notes/articles/' . $table_type . '/' . $table_id . '.json');
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->RequestHandler = new NotesControllerMockRequestHandlerComponent();
		$this->Notes->RequestHandler->setReturnValue('prefers', true);

		$this->Notes->articles($table_type, $table_id);

		$this->assertEqual($this->Notes->error, 'invalid_field');
	}

	function testArticlesAccessDenied()
	{
		$table_type = 'group';
		$table_id = 2;

		$this->Notes->params = Router::parse('notes/articles/' . $table_type . '/' . $table_id . '.json');
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->RequestHandler = new NotesControllerMockRequestHandlerComponent();
		$this->Notes->RequestHandler->setReturnValue('prefers', true);

		$this->Notes->articles($table_type, $table_id);

		$this->assertEqual($this->Notes->error, 'access_denied');
	}

	function testArticlesInvalidLimit()
	{
		$table_type = 'group';
		$table_id = 1;
		$limit = 'invalid';

		$this->Notes->params = Router::parse('notes/articles/' . $table_type . '/' . $table_id . '.json');
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->RequestHandler = new NotesControllerMockRequestHandlerComponent();
		$this->Notes->RequestHandler->setReturnValue('prefers', true);

		$this->Notes->params['form']['limit'] = $limit;
		$this->Notes->articles($table_type, $table_id);

		$this->assertEqual($this->Notes->error, 'invalid_field');
	}

	function testArticlesInvalidStart()
	{
		$table_type = 'group';
		$table_id = 1;
		$start = 'invalid';

		$this->Notes->params = Router::parse('notes/articles/' . $table_type . '/' . $table_id . '.json');
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->RequestHandler = new NotesControllerMockRequestHandlerComponent();
		$this->Notes->RequestHandler->setReturnValue('prefers', true);

		$this->Notes->params['form']['start'] = $start;
		$this->Notes->articles($table_type, $table_id);

		$this->assertEqual($this->Notes->error, 'invalid_field');
	}

	function testAdd()
	{
		$table_type = 'group';
		$table_id = 1;

		$this->Notes->data = array(
			'Note' => array(
				'title' => 'Added Note',
				'content' => 'Added Note Content',
			),
		);

		$this->Notes->params = Router::parse('notes/add/' . $table_type . '/' . $table_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->add($table_type, $table_id);

		$conditions = array(
			'Note.table_type' => $table_type,
			'Note.table_id' => $table_id,
			'Note.title' => 'Added Note',
			'Note.content' => 'Added Note Content',
		);
		$this->Notes->Note->recursive = -1;
		$result = $this->Notes->Note->find('first', array('conditions' => $conditions));
		$this->assertFalse(empty($result));

		$article_id = $result['Note']['id'];

		$this->assertEqual($this->Notes->redirectUrl, '/notes/view/' . $article_id);
	}

	function testAddNullTableType()
	{
		$table_type = null;
		$table_id = 1;

		$this->Notes->params = Router::parse('notes/add/' . $table_type . '/' . $table_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->add($table_type, $table_id);

		$this->assertEqual($this->Notes->error, 'missing_field');
	}

	function testAddInvalidTableType()
	{
		$table_type = 'invalid';
		$table_id = 1;

		$this->Notes->params = Router::parse('notes/add/' . $table_type . '/' . $table_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->add($table_type, $table_id);

		$this->assertEqual($this->Notes->error, 'invalid_field');
	}

	function testAddNullTableId()
	{
		$table_type = 'group';
		$table_id = null;

		$this->Notes->params = Router::parse('notes/add/' . $table_type . '/' . $table_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->add($table_type, $table_id);

		$this->assertEqual($this->Notes->error, 'missing_field');
	}

	function testAddInvalidTableId()
	{
		$table_type = 'group';
		$table_id = 'invalid';

		$this->Notes->params = Router::parse('notes/add/' . $table_type . '/' . $table_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->add($table_type, $table_id);

		$this->assertEqual($this->Notes->error, 'invalid_field');
	}

	function testAddAccessDenied()
	{
		$table_type = 'group';
		$table_id = 2;

		$this->Notes->params = Router::parse('notes/add/' . $table_type . '/' . $table_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->add($table_type, $table_id);

		$this->assertEqual($this->Notes->error, 'access_denied');
	}

	function testEdit()
	{
		$article_id = 2;

		$this->Notes->data = array(
			'Note' => array(
				'content' => 'Edited Home Content',
			),
		);

		$this->Notes->params = Router::parse('notes/edit/' . $article_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->edit($article_id);

		$conditions = array(
			'Note.id' => $article_id,
		);
		$this->Notes->Note->recursive = -1;
		$result = $this->Notes->Note->find('first', array('conditions' => $conditions));
		$this->assertFalse(empty($result));

		$expected = array(
			'Note' => array(
				'id'  => $result['Note']['id'],
				'table_type' => 'group',
				'table_id'  => 1,
				'title'  => 'Home',
				'content'  => 'Edited Home Content',
				'created'  => $result['Note']['created'],
				'modified'  => $result['Note']['modified'],
				'permanent'  => 1,
				'read_only'  => 0
			),
		);
		$this->assertEqual($result, $expected);

		$this->assertEqual($this->Notes->redirectUrl, '/notes/view/' . $article_id);
	}

	function testEditJson()
	{
		$article_id = 2;

		$this->Notes->params = Router::parse('notes/edit/' . $article_id . '.json');
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->RequestHandler = new NotesControllerMockRequestHandlerComponent();
		$this->Notes->RequestHandler->setReturnValue('prefers', true);

		$this->Notes->edit($article_id);

		$this->assertTrue(isset($this->Notes->viewVars['node']));
		$node = $this->Notes->viewVars['node'];

		$expected = array(
			'id'  => $node['id'],
			'table_id'  => 1,
			'table_type' => 'group',
			'title'  => 'Home',
			'created'  => $node['created'],
			'modified'  => $node['modified'],
			'content'  => 'Welcome Home',
			'permanent'  => 1,
			'group' => 'group: 1',
		);

		$this->assertEqual($node, $expected);
	}


	function testEditNullArticleId()
	{
		$article_id = null;

		$this->Notes->params = Router::parse('notes/edit/' . $article_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->edit($article_id);

		$this->assertEqual($this->Notes->error, 'missing_field');
	}

	function testEditInvalidArticleId()
	{
		$article_id = 'invalid';

		$this->Notes->params = Router::parse('notes/edit/' . $article_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->edit($article_id);

		$this->assertEqual($this->Notes->error, 'invalid_field');
	}

	function testEditAccessDenied()
	{
		$article_id = 4;

		$this->Notes->params = Router::parse('notes/edit/' . $article_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->edit($article_id);

		$this->assertEqual($this->Notes->error, 'access_denied');
	}

	function testDelete()
	{
		$article_id = 5;

		$this->Notes->params = Router::parse('notes/delete/' . $article_id . '.json');
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->RequestHandler = new NotesControllerMockRequestHandlerComponent();
		$this->Notes->RequestHandler->setReturnValue('prefers', true);

		$this->Notes->delete($article_id);

		$this->assertTrue(isset($this->Notes->viewVars['response']));
		$response = $this->Notes->viewVars['response'];
		$this->assertTrue($response['success']);

		$conditions = array(
			'Note.id' => $article_id,
		);
		$this->Notes->Note->recursive = -1;
		$result = $this->Notes->Note->find('first', array('conditions' => $conditions));
		$this->assertTrue(empty($result));
	}

	function testDeleteNotJson()
	{
		$article_id = 5;

		$this->Notes->params = Router::parse('notes/delete/' . $article_id);
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->delete($article_id);

		$this->assertEqual($this->Notes->error, 'error404');
	}

	function testDeleteNullArticleId()
	{
		$article_id = null;

		$this->Notes->params = Router::parse('notes/delete/' . $article_id . '.json');
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->RequestHandler = new NotesControllerMockRequestHandlerComponent();
		$this->Notes->RequestHandler->setReturnValue('prefers', true);

		$this->Notes->delete($article_id);

		$this->assertEqual($this->Notes->error, 'missing_field');
	}

	function testDeleteInvalidArticleId()
	{
		$article_id = 'invalid';

		$this->Notes->params = Router::parse('notes/delete/' . $article_id . '.json');
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->RequestHandler = new NotesControllerMockRequestHandlerComponent();
		$this->Notes->RequestHandler->setReturnValue('prefers', true);

		$this->Notes->delete($article_id);

		$this->assertEqual($this->Notes->error, 'invalid_field');
	}

	function testDeleteAccessDenied()
	{
		$article_id = 4;

		$this->Notes->params = Router::parse('notes/delete/' . $article_id . '.json');
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->RequestHandler = new NotesControllerMockRequestHandlerComponent();
		$this->Notes->RequestHandler->setReturnValue('prefers', true);

		$this->Notes->delete($article_id);

		$this->assertEqual($this->Notes->error, 'access_denied');
	}

	function testView()
	{
		$article_id = 2;

		$this->Notes->params = Router::parse('notes/view/' . $article_id . '.json');
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->RequestHandler = new NotesControllerMockRequestHandlerComponent();
		$this->Notes->RequestHandler->setReturnValue('prefers', true);

		$this->Notes->view($article_id);

		$this->assertTrue(isset($this->Notes->viewVars['node']));
		$node = $this->Notes->viewVars['node'];

		$expected = array(
			'id'  => $node['id'],
			'table_id'  => 1,
			'table_type' => 'group',
			'title'  => 'Home',
			'created'  => $node['created'],
			'modified'  => $node['modified'],
			'content'  => 'Welcome Home',
			'permanent'  => 1,
			'group' => 'group: 1',
		);
		$this->assertEqual($node, $expected);
	}

	function testViewNullArticleId()
	{
		$article_id = null;

		$this->Notes->params = Router::parse('notes/view/' . $article_id . '.json');
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->RequestHandler = new NotesControllerMockRequestHandlerComponent();
		$this->Notes->RequestHandler->setReturnValue('prefers', true);

		$this->Notes->view($article_id);

		$this->assertEqual($this->Notes->error, 'missing_field');
	}

	function testViewInvalidArticleId()
	{
		$article_id = 'invalid';

		$this->Notes->params = Router::parse('notes/view/' . $article_id . '.json');
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->RequestHandler = new NotesControllerMockRequestHandlerComponent();
		$this->Notes->RequestHandler->setReturnValue('prefers', true);

		$this->Notes->view($article_id);

		$this->assertEqual($this->Notes->error, 'invalid_field');
	}

	function testViewAccessDenied()
	{
		$article_id = 4;

		$this->Notes->params = Router::parse('notes/view/' . $article_id . '.json');
		$this->Notes->beforeFilter();
		$this->Notes->Component->startup($this->Notes);

		$this->Notes->RequestHandler = new NotesControllerMockRequestHandlerComponent();
		$this->Notes->RequestHandler->setReturnValue('prefers', true);

		$this->Notes->view($article_id);

		$this->assertEqual($this->Notes->error, 'access_denied');
	}


	function endTest() {
		unset($this->Notes);
		ClassRegistry::flush();	
	}
}
?>
