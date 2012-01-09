<?php
	/* Define Constants */
	if(!defined('NOTES_APP'))
	{
		define('NOTES_APP', APP . DS . 'plugins' . DS . 'notes');
	}

	if(!defined('NOTES_CONFIGS'))
	{
		define('NOTES_CONFIGS', NOTES_APP . DS . 'config');
	}

	/* Include Config File */
	require_once(NOTES_CONFIGS . DS . 'notes.php');

	/* Setup Permissions */
	try {
		$parent = $this->addPermission('notes', 'Note');
		$this->addPermission('notes.view', 'View Note', 1, $parent);

		$this->addPermission('notes.add', 'Add Note', 2, $parent);
		$this->addPermission('notes.edit', 'Edit Note', 4, $parent);
		$this->addPermission('notes.delete', 'Delete Note', 8, $parent);

		$this->addPermissionDefaults(array(
			'group' => array(
				'notes' => array(
					'Administrator' => 15,
					'Manager' => 15,
					'Member' => 3,
				),
			),
			'project' => array(
				'notes' => array(
					'Administrator' => 15,
					'Manager' => 15,
					'Member' => 3,
				),
			),
		));
	} catch(Exception $e) {
		// TODO; Do something
	}

	/* Add Listeners */
	try {
		$this->addListener('notes', 'group.add', function($id, $name) {
			App::import('Model', 'Notes.Note');
			$note = new Note();

			$data = array(
				'Note' => array(
					'table_type' => 'group',
					'table_id' => $id,
					'title' => 'Welcome',
					'content' => $note->welcome,
					'permanent' => 1,
				),
			);
			$note->create();
			if(!$note->save($data))
			{
				throw new RuntimeException('Unable to save note root');
			}
		});

		$this->addListener('notes', 'group.delete', function($id, $name) {
			App::import('Model', 'Notes.Note');
			$note = new Note();

			if(!$note->deleteAll(array(
				'Note.table_type' => 'group',
				'Note.table_id' => $id,
			), true))
			{
				throw new RuntimeException('Unable to delete notes');
			}
		});

		$this->addListener('notes', 'project.add', function($id, $name) {
			App::import('Model', 'Notes.Note');
			$note = new Note();

			$data = array(
				'Note' => array(
					'table_type' => 'project',
					'table_id' => $id,
					'title' => 'Welcome',
					'content' => $note->welcome,
					'permanent' => 1,
				),
			);
			$note->create();
			if(!$note->save($data))
			{
				throw new RuntimeException('Unable to save note root');
			}
		});

		$this->addListener('notes', 'project.delete', function($id, $name) {
			App::import('Model', 'Notes.Note');
			$note = new Note();

			if(!$note->deleteAll(array(
				'Note.table_type' => 'project',
				'Note.table_id' => $id,
			)))
			{
				throw new RuntimeException('Unable to delete notes');
			}
		});
	} catch(Exception $e) {
		// TODO: Do something
	}
?>
