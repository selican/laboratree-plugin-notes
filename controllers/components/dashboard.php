<?php
class DashboardComponent extends Object
{
	var $uses = array(
		'Notes.Note',
		'GroupsUsers',
		'ProjectsUsers',
	);

	function _loadModels(&$object)
	{
		foreach($object->uses as $modelClass)
		{
			$plugin = null;

			if(strpos($modelClass, '.') !== false)
			{
				list($plugin, $modelClass) = explode('.', $modelClass);
				$plugin = $plugin . '.';
			}

			App::import('Model', $plugin . $modelClass);
			$this->{$modelClass} = new $modelClass();

			if(!$this->{$modelClass})
			{
				return false;
			}
		}
	}

	function initialize(&$controller, $settings = array())
	{
		$this->Controller =& $controller;
		$this->_loadModels($this);
	}

	function startup(&$controller) {}

	function process($table_type, $table_id, $params = array())
	{
		$notes = array();

		if($table_type == 'user')
		{
			try {
				$articles = $this->Note->articles('user', $table_id);
			} catch(Exception $e) {
				throw new RuntimeException('Unable to retrieve articles');
			}

			$notes = array_merge($notes, $articles);

			try {
				$groups = $this->GroupsUsers->groups($table_id);
			} catch(Exception $e) {
				throw new RuntimeException('Unable to retrieve groups');
				return;
			}

			foreach($groups as $group)
			{
				try {
					$articles = $this->Note->articles('group', $group['Group']['id']);
				} catch(Exception $e) {
					throw new RuntimeException('Unable to retrieve articles');
				}

				$notes = array_merge($notes, $articles);
			}

			try {
				$projects = $this->ProjectsUsers->projects($table_id);
			} catch(Exception $e) {
				throw new RuntimeException('Unable to retrieve projects');
			}

			foreach($projects as $project)
			{
				try {
					$articles = $this->Note->articles('project', $project['Project']['id']);
				} catch(Exception $e) {
					$this->cakeError('internal_error', array('action' => 'Retrieve', 'resource' => 'Notes'));
					return;
				}

				$notes = array_merge($notes, $articles);
			}

			usort($notes, array($this->Note, 'sort'));
		}
		else
		{
			try {
				$notes = $this->Note->articles($table_type, $table_id);
			} catch(Exception $e) {
				throw new Exception($e);
			}
		}

		try {
			$list = $this->Note->toList('notes', $notes);
		} catch(Exception $e) {
			throw new Exception($e);
		}

		return $list;
	}
}
?>
