<?php
class Note extends NotesAppModel
{
	var $name = 'Note';

	var $html = array(
		'content',
	);

	var $validate = array(
		'table_id' => array(
			'table_id-1' => array(
				'rule' => 'notEmpty',
				'message' => 'Owner ID must not be empty.',
			),
			'table_id-2' => array(
				'rule' => 'numeric',
				'message' => 'Owner ID must be a number.',
			),
			'table_id-3' => array(
				'rule' => array('maxLength', 10),
				'message' => 'Table ID must be 10 characters or less.',
			),
		),
		'table_type' => array(
			'table_type-1' => array(
				'rule' => 'notEmpty',
				'message' => 'Type must not be empty.',
			),
			'table_type-2' => array(
				'rule' => array('inList', array('user', 'group', 'project')),
			),
		),
		'title' => array(
			'title-1' => array(
				'rule' => 'notEmpty',
				'message' => 'Title must not be empty.',
			),
			'title-2' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Title must not be longer than 255 characters.',
			),
		),
	);

	var $belongsTo = array(
		'Group' => array(
			'className' => 'Group',
			'foreignKey' => 'table_id',
		),
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'table_id',
		),
	);

	var $welcome = '<p style="font-weight: bold; font-size: 1.5em; text-align: center;">Welcome to Notes</p><p style="font-size: 1.2em; text-align: center; margin-top: 10px;">Notes is a feature that allows group and project members to collect and share information.</p><ul style="margin-left: auto; margin-right: auto; font-size: 1.2em; width: 100px; margin-top: 15px; list-style: circle;"><li><a href="#" style="text-decoration: underline;">Getting Started</a></li><li><a href="#" style="text-decoration: underline;">Add a Note</a></li><li><a href="#" style="text-decoration: underline;">Edit this Note</a></li></ul>';

	/**
	 * List Notes
	 *
	 * @param string  $table_type Table Type
	 * @param integer $table_id   Table ID
	 * @param integer $recursion  Recursion
	 *
	 * @return array Notes
	 */
	function articles($table_type, $table_id, $recursion = 1)
	{
		if(!is_string($table_type) || !in_array($table_type, array('user', 'group', 'project')))
		{
			throw new InvalidArgumentException('Invalid table type.');
		}

		if(!is_numeric($table_id) || $table_id < 1)
		{
			throw new InvalidArgumentException('Invalid table id.');
		}

		return $this->find('all', array(
			'conditions' => array(
				$this->name . '.table_type' => $table_type,
				$this->name . '.table_id' => $table_id,
			),
			'order' => $this->name . '.title',
			'recursive' => $recursion,
		));
	}

	/**
	 * Converts a record to an ExtJS Store node
	 *
	 * @param array $article Article
	 * @param array $params  Parameters
	 *
	 * @return array ExtJS Store Node
	 */
	function toNode($article, $params = array())
	{
		if(empty($article))
		{
			throw new InvalidArgumentException('Invalid Article');
		}

		if(!is_array($article))
		{
			throw new InvalidArgumentException('Invalid Article');
		}

		if(!empty($params))
		{
			if(!is_array($params))
			{
				throw new InvalidArgumentException('Invalid Parameters');
			}
		}

		if(!isset($params['model']))
		{
			$params['model'] = $this->name;
		}

		if(!is_string($params['model']))
		{
			throw new RuntimeException('Invalid Model');
		}

		$model = $params['model'];

		if(!isset($article[$model]))
		{
			throw new InvalidArgumentException('Invalid Model Key');
		}

		$required = array(
			'id',
			'table_id',
			'table_type',
			'title',
			'created',
		);

		foreach($required as $key)
		{
			if(!array_key_exists($key, $article[$model]))
			{
				throw new InvalidArgumentException('Missing ' . strtoupper($key) . ' Key');
			}
		}

		$node = array(
			'id' => $article[$model]['id'],
			'table_id' => $article[$model]['table_id'],
			'table_type' => $article[$model]['table_type'],
			'title' => $article[$model]['title'],
			'created' => date('m/d/Y g:ia', strtotime($article[$model]['created'])),
			'modified' => date('m/d/Y g:ia', strtotime($article[$model]['modified'])),
			'content' => $article[$model]['content'],
			'group' => $article[$model]['table_type'] . ': ' . $article[$model]['table_id'],
		);

		$group = Inflector::camelize($article[$model]['table_type']);

		if(isset($article[$group]))
		{
			$node['group'] = $group . ': ' . $article[$group]['name'];
		}

		if(isset($params['values']))
		{
			if(is_array($params['values']))
			{
				$node = array_merge($node, $params['values']);
			}
		}

		return $node;
	}

	/**
	 * Sorts Notes
	 *
	 * @param array $a Note
	 * @param array $b Note
	 *
	 * @throws InvalidArgumentException
	 *
	 * @return integer Sort Priority
	 */
	function sort($a, $b)
	{
		if(empty($a))
		{
			throw new InvalidArgumentException('Invalid Note');
		}

		if(!is_array($a))
		{
			throw new InvalidArgumentException('Invalid Note');
		}

		if(empty($b))
		{
			throw new InvalidArgumentException('Invalid Note');
		}

		if(!is_array($b))
		{
			throw new InvalidArgumentException('Invalid Note');
		}

		return strcasecmp($a['Note']['title'], $b['Note']['title']);
	}
}
?>
