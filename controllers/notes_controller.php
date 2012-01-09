<?php
class NotesController extends NotesAppController
{
	var $name = 'Notes';

	var $uses = array(
		'Notes.Note',
		'User',
		'Group',
		'Project',
	);

	var $components = array(
		'Auth',
		'Security',
		'Session',
		'RequestHandler',
		'Plugin',
	);

	function beforeFilter()
	{
		$this->Security->validatePost = false;
		
		parent::beforeFilter();
	}

	/**
	 * Redirects to action based on Table Type
	 *
	 * @param string  $table_type Table Type
	 * @param integer $table_id   Table ID
	 */
	function index($table_type = '', $table_id = '')
	{
		if(empty($table_type))
		{
			$this->cakeError('missing_field', array('field' => 'Table Type'));
			return;
		}

		if(!in_array($table_type, array('group', 'project')))
		{
			$this->cakeError('invalid_field', array('field' => 'Table Type'));
			return;
		}

		if(empty($table_id))
		{
			$this->cakeError('missing_field', array('field' => 'Table ID'));
			return;
		}

		if(!is_numeric($table_id) || $table_id < 1)
		{
			$this->cakeError('invalid_field', array('field' => 'Table ID'));
			return;
		}

		if(method_exists($this, $table_type))
		{
			$this->redirect('/notes/' . $table_type . '/' . $table_id);
			return;
		}

		$this->cakeError('invalid_field', array('field' => 'Table Type'));
		return;
	}

	/**
	 * Group Notes
	 *
	 * @param integer $group_id Group ID
	 */
	function group($group_id = '')
	{
		if(empty($group_id))
		{
			$this->cakeError('missing_field', array('field' => 'Group ID'));
			return;
		}

		if(!is_numeric($group_id) || $group_id < 1)
		{
			$this->cakeError('invalid_field', array('field' => 'Group ID'));
			return;
		}

		$group = $this->Group->find('first', array(
			'conditions' => array(
				'Group.id' => $group_id,
			),
			'recursive' => -1,
		));
		if(empty($group))
		{
			$this->cakeError('invalid_field', array('field' => 'Group ID'));
			return;
		}

		$permission = $this->PermissionCmp->check('note.view', 'group', $group_id);
		if(!$permission)
		{
			$this->cakeError('access_denied', array('action' => 'View', 'resource' => 'Note'));
			return;
		}

		$article = $this->Note->find('first', array(
			'conditions' => array(
				'Note.table_type' => 'group',
				'Note.table_id' => $group_id,
				'Note.title' => 'Home',
			),
			'recursive' => -1,
		));

		$article_id = $article['Note']['id'];
		if(empty($article_id))
		{
			$this->redirect('/notes/articles/group/' . $group_id);
			return;
		}
		else
		{
			$this->redirect('/notes/view/' . $article_id);
			return;
		}
	}

	/**
	 * Project Notes
	 *
	 * @param integer $project_id Project ID
	 */
	function project($project_id = '')
	{
		if(empty($project_id))
		{
			$this->cakeError('missing_field', array('field' => 'Project ID'));
			return;
		}

		if(!is_numeric($project_id) || $project_id < 1)
		{
			$this->cakeError('invalid_field', array('field' => 'Project ID'));
			return;
		}

		$project = $this->Project->find('first', array(
			'conditions' => array(
				'Project.id' => $project_id,
			),
			'recursive' => -1,
		));
		if(empty($project))
		{
			$this->cakeError('invalid_field', array('field' => 'Project ID'));
			return;
		}

		$permission = $this->PermissionCmp->check('note.view', 'project', $project_id);
		if(!$permission)
		{
			$this->cakeError('access_denied', array('action' => 'View', 'resource' => 'Note'));
			return;
		}

		$group = $this->Group->find('first', array(
			'conditions' => array(
				'Group.id' => $project['Project']['group_id'],
			),
			'recursive' => -1,
		));
		if(empty($group))
		{
			$this->cakeError('internal_error', array('action' => 'View', 'resource' => 'Project Notes'));
			return;
		}

		$this->set('group_name', $group['Group']['name']);
		$this->set('group_id', $group['Group']['id']);

		$article = $this->Note->find('first', array(
			'conditions' => array(
				'Note.table_type' => 'project',
				'Note.table_id' => $project_id,
				'Note.title' => 'Home',
			),
			'recursive' => -1,
		));

		$article_id = $article['Note']['id'];
		if(empty($article_id))
		{
			$this->redirect('/notes/articles/project/' . $project_id);
			return;
		}
		else
		{
			$this->redirect('/notes/view/' . $article_id);
			return;
		}
	}

	/**
	 * List Notes for a Group or Project
	 *
	 * @param string  $table_type Table Type
	 * @param integer $table_id   Table ID
	 */
	function articles($table_type = '', $table_id = '')
	{
		if(empty($table_type))
		{
			$this->cakeError('missing_field', array('field' => 'Table Type'));
			return;
		}

		if(!is_string($table_type) || !in_array($table_type, array('group', 'project')))
		{
			$this->cakeError('invalid_field', array('field' => 'Table Type'));
			return;
		}

		if(empty($table_id))
		{
			$this->cakeError('missing_field', array('field' => 'Table ID'));
			return;
		}

		if(!is_numeric($table_id) || $table_id < 1)
		{
			$this->cakeError('invalid_field', array('field' => 'Table ID'));
			return;
		}

		$permission = $this->PermissionCmp->check('note.view', $table_type, $table_id);
		if(!$permission)
		{
			$this->cakeError('access_denied', array('action' => 'View', 'resource' => 'Note'));
			return;
		}

		$name = null;
		switch($table_type)
		{
			case 'group':
				$this->Group->id = $table_id;
				$name = $this->Group->field('name');
				$this->set('group_id', $table_id);
				break;
			case 'project':
				$this->Project->id = $table_id;
				$name = $this->Project->field('name');
				$group_id = $this->Project->field('group_id');

				$this->set('project_id', $table_id);

				$group = $this->Group->find('first', array(
					'conditions' => array(
						'Group.id' => $group_id,
					),
					'recursive' => -1,
				));
				if(empty($group))
				{
					$this->cakeError('internal_error', array('action' => 'View', 'resource' => 'Note'));
					return;
				}

				$this->set('group_name', $group['Group']['name']);
				$this->set('group_id', $group['Group']['id']);
				break;
			default:
				$this->cakeError('invalid_field', array('field' => 'Table Type'));
				return;
		}

		$this->pageTitle = 'Notes - ' . $name;
		$this->set('pageName', $name . ' - Notes');

		$this->set('name', $name);

		$this->set('table_id', $table_id);
		$this->set('table_type', $table_type);

		$context = array(
			'table_type' => $table_type,
			'table_id' => $table_id,
			'permissions' => array(
				'note' => $permission['mask'],
			),
		);
		$this->set('context', $context);

		if($this->RequestHandler->prefers('json'))
		{
			$limit = 30;
			if(isset($this->params['form']['limit']))
			{
				$limit = $this->params['form']['limit'];
			}

			if(!is_numeric($limit) || $limit < 1)
			{
				$this->cakeError('invalid_field', array('field' => 'Limit'));
				return;
			}

			$start = 0;
			if(isset($this->params['form']['start']))
			{
				$start = $this->params['form']['start'];
			}

			if(!is_numeric($start) || $start < 0)
			{
				$this->cakeError('invalid_field', array('field' => 'Start'));
				return;
			}

			$articles = $this->Note->find('all', array(
				'conditions' => array(
					'Note.table_type' => $table_type,
					'Note.table_id' => $table_id,
				),
				'contain' => array(
					'Group.Role.Perm',
					'Project.Role.Perm',
				),
				'order' => 'Note.Created DESC',
				'limit' => $limit,
				'offset' => $start,
			));

			try {
				$response = $this->Note->toList('articles', $articles);
			} catch(Exception $e) {
				$this->cakeError('internal_error', array('action' => 'Convert', 'resource' => 'Notes'));
				return;
			}

			$this->set('response', $response);
		}
	}

	/**
	 * Add Note to Group or Project
	 *
	 * @param string  $table_type Table Type
	 * @param integer $table_id   Table ID
	 */
	function add($table_type = '', $table_id = '')
	{
		if(empty($table_type))
		{
			$this->cakeError('missing_field', array('field' => 'Table Type'));
			return;
		}

		if(!is_string($table_type) || !in_array($table_type, array('group', 'project')))
		{
			$this->cakeError('invalid_field', array('field' => 'Table Type'));
			return;
		}

		if(empty($table_id))
		{
			$this->cakeError('missing_field', array('field' => 'Table ID'));
			return;
		}

		if(!is_numeric($table_id) || $table_id < 1)
		{
			$this->cakeError('invalid_field', array('field' => 'Table ID'));
			return;
		}
		$permission = $this->PermissionCmp->check('note.add', $table_type, $table_id);
		if(!$permission)
		{
			$this->cakeError('access_denied', array('action' => 'Add', 'resource' => 'Notes'));
			return;
		}

		$name = null;
		switch($table_type)
		{
			case 'group':
				$this->Group->id = $table_id;
				$name = $this->Group->field('name');
				$this->set('group_id', $table_id);
				break;
			case 'project':
				$this->Project->id = $table_id;
				$name = $this->Project->field('name');
				$group_id = $this->Project->field('group_id');

				$this->set('project_id', $table_id);

				$group = $this->Group->find('first', array(
					'conditions' => array(
						'Group.id' => $group_id,
					),
					'recursive' => -1,
				));
				if(empty($group))
				{
					$this->cakeError('internal_error', array('action' => 'Add', 'resource' => 'Notes'));
					return;
				}

				$this->set('group_name', $group['Group']['name']);
				$this->set('group_id', $group['Group']['id']);
				break;
			default:
				$this->cakeError('invalid_field', array('field' => 'Table Type'));
				return;
		}
		
		$this->pageTitle = 'Add Note - ' . $name;
		$this->set('pageName', $name . ' - Add Note');

		$this->set('table_type', $table_type);
		$this->set('table_id', $table_id);
		$this->set('name', $name);

		$context = array(
			'table_type' => $table_type,
			'table_id' => $table_id,
			'permissions' => array(
				'note' => $permission['mask'],
			),
		);
		$this->set('context', $context);

		if(!empty($this->data))
		{
			$this->data['Note']['table_type'] = $table_type;
			$this->data['Note']['table_id'] = $table_id;

			$this->Note->create();
			if($this->Note->save($this->data))
			{
				$article_id = $this->Note->id;

				try {
					$this->Plugin->broadcastListeners('notes.add', array(
						$article_id,
						$table_type,
						$table_id,
						$this->data['Note']['title'],
					));
				} catch(Exception $e) {
					$this->cakeError('internal_error', array('action' => 'Add', 'resource' => 'Note'));
					return;
				}

				$this->redirect('/notes/view/' . $article_id);
				return;
			}
		}
	}

	/**
	 * Edit Note
	 *
	 * @param integer $article_id Note ID
	 */
	function edit($article_id = '')
	{
		if(empty($article_id))
		{
			$this->cakeError('missing_field', array('field' => 'Note ID'));
			return;
		}

		if(!is_numeric($article_id) || $article_id < 1)
		{
			$this->cakeError('invalid_field', array('field' => 'Note ID'));
			return;
		}

		$article = $this->Note->find('first', array(
			'conditions' => array(
				'Note.id' => $article_id,
			),
			'recursive' => -1,
		));
		if(empty($article))
		{
			$this->cakeError('invalid_field', array('field' => 'Note ID'));
			return;
		}

		$permission = $this->PermissionCmp->check('note.edit', $article['Note']['table_type'], $article['Note']['table_id']);
		if(!$permission)
		{
			$this->cakeError('access_denied', array('action' => 'Edit', 'resource' => 'Note'));
			return;
		}

		$name = null;
		switch($article['Note']['table_type'])
		{
			case 'group':
				$this->Group->id = $article['Note']['table_id'];
				$name = $this->Group->field('name');
				$this->set('group_id', $article['Note']['table_id']);
				break;
			case 'project':
				$this->Project->id = $article['Note']['table_id'];
				$name = $this->Project->field('name');
				$group_id = $this->Project->field('group_id');

				$this->set('project_id', $article['Note']['table_id']);

				$group = $this->Group->find('first', array(
					'conditions' => array(
						'Group.id' => $group_id,
					),
					'recursive' => -1,
				));
				if(empty($group))
				{
					$this->cakeError('internal_error', array('action' => 'Edit', 'resource' => 'Note'));
					return;
				}

				$this->set('group_name', $group['Group']['name']);
				$this->set('group_id', $group['Group']['id']);
				break;
			default:
				$this->cakeError('invalid_field', array('field' => 'Table Type'));
				return;
		}

		$this->pageTitle = 'Edit Note - ' . $article['Note']['title'];
		$this->set('pageName', $name . ' - Edit Note - ' . $article['Note']['title']);	

		$this->set('name', $name);
		$this->set('article', $article);
		$this->set('article_id', $article_id);

		$context = array(
			'article_id' => $article_id,
			'permissions' => array(
				'note' => $permission['mask'],
			),
		);
		$this->set('context', $context);

		if(!empty($this->data))
		{
			$this->data['Note']['id'] = $article_id;
				
			if($this->Note->save($this->data))
			{
				try {
					$this->Plugin->broadcastListeners('notes.edit', array(
						$article_id,
						$article['Note']['table_type'],
						$article['Note']['table_id'],
						$this->data['Note']['title'],
					));
				} catch(Exception $e) {
					$this->cakeError('internal_error', array('action' => 'Edit', 'resource' => 'Note'));
					return;
				}

				$this->redirect('/notes/view/' . $article_id);
				return;
			}
		}
		
		if($this->RequestHandler->prefers('json'))
		{
			$article = $this->Note->find('first', array(
				'conditions' => array(
					'Note.id' => $article_id,
				),
				'recursive' => -1,
			));

			try {
				$node = $this->Note->toNode($article);
			} catch(Exception $e) {
				$this->cakeError('internal_error', array('action' => 'Convert', 'resource' => 'Note'));
				return;
			}
			$this->set('node', $node);
		}
	}

	/**
	 * Delte Note
	 *
	 * @param integer $article_id Article ID
	 */
	function delete($article_id = '')
	{
		if(!$this->RequestHandler->prefers('json'))
		{
			$this->cakeError('error404');
			return;
		}

		if(empty($article_id))
		{
			$this->cakeError('missing_field', array('field' => 'Article ID'));
			return;
		}

		if(!is_numeric($article_id) || $article_id < 1)
		{
			$this->cakeError('invalid_field', array('field' => 'Article ID'));
			return;
		}

		$article = $this->Note->find('first', array(
			'conditions' => array(
				'Note.id' => $article_id,
			),
			'recursive' => -1,
		));
		if(empty($article))
		{
			$this->cakeError('invalid_field', array('field' => 'Article ID'));
			return;
		}
		
		$permission = $this->PermissionCmp->check('note.delete', $article['Note']['table_type'], $article['Note']['table_id']);
		if(!$permission)
		{
			$this->cakeError('access_denied', array('action' => 'Delete', 'resource' => 'Note'));
			return;
		}

		$this->Note->delete($article_id);

		try {
			$this->Plugin->broadcastListeners('notes.delete', array(
				$article_id,
				$article['Note']['table_type'],
				$article['Note']['table_id'],
				$article['Note']['title'],
			));
		} catch(Exception $e) {
			$this->cakeError('internal_error', array('action' => 'Delete', 'resource' => 'Note'));
			return;
		}

		$response = array(
			'success' => true,
		);

		$this->set('response', $response);
	}

	/**
	 * View Note
	 *
	 * @param integer $article_id Article ID
	 */
	function view($article_id = '')
	{
		if(empty($article_id))
		{
			$this->cakeError('missing_field', array('field' => 'Article ID'));
			return;
		}

		if(!is_numeric($article_id) || $article_id < 1)
		{
			$this->cakeError('invalid_field', array('field' => 'Article ID'));
			return;
		}

		$article = $this->Note->find('first', array(
			'conditions' => array(
				'Note.id' => $article_id,
			),
			'recursive' => -1,
		));
		if(empty($article))
		{
			$this->cakeError('invalid_field', array('field' => 'Article ID'));
			return;
		}
		
		$permission = $this->PermissionCmp->check('note.view', $article['Note']['table_type'], $article['Note']['table_id']);
		if(!$permission)
		{
			$this->cakeError('access_denied', array('action' => 'Delete', 'resource' => 'Note'));
			return;
		}

		$name = null;
		switch($article['Note']['table_type'])
		{
			case 'group':
				$this->Group->id = $article['Note']['table_id'];
				$name = $this->Group->field('name');
				$this->set('group_id', $article['Note']['table_id']);
				break;
			case 'project':
				$this->Project->id = $article['Note']['table_id'];
				$name = $this->Project->field('name');
				$group_id = $this->Project->field('group_id');

				$this->set('project_id', $article['Note']['table_id']);

				$group = $this->Group->find('first', array(
					'conditions' => array(
						'Group.id' => $group_id,
					),
					'recursive' => -1,
				));
				if(empty($group))
				{
					$this->cakeError('internal_error', array('action' => 'Delete', 'resource' => 'Note'));
					return;
				}

				$this->set('group_name', $group['Group']['name']);
				$this->set('group_id', $group['Group']['id']);
				break;
			default:
				$this->cakeError('invalid_field', array('field' => 'Table Type'));
				return;
		}

		$this->pageTitle = 'View Note - ' . $article['Note']['title'] . ' - ' . $name;

		$this->set('pageName', $name . ' - View Note - ' . $article['Note']['title']); 
		$this->set('name', $name);

		$this->set('article', $article);
		$this->set('article_id', $article_id);

		$context = array(
			'article_id' => $article_id,
			'permissions' => array(
				'note' => $permission['mask'],
			),
		);
		$this->set('context', $context);

		if($this->RequestHandler->prefers('json'))
		{
			try {
				$node = $this->Note->toNode($article);
			} catch(Exception $e) {
				$this->cakeError('internal_error', array('action' => 'Convert', 'resource' => 'Note'));
				return;
			}
			$this->set('node', $node);
		
		}
	}

	/**
	 * Help for Add
	 */
	function help_add()
	{
		$this->pageTitle = 'Help - Add - Notes';
		$this->set('pageName', 'Notes - Add - Help');
	}

	/**
	 * Help for Edit
	 */
	function help_edit()
	{
		$this->pageTitle = 'Help - Edit - Notes';
		$this->set('pageName', 'Notes - Edit - Help');
	}

	/* TODO: Add Help for other Actions */
}
?>
