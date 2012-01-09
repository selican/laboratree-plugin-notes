<?php
	$type = Inflector::pluralize(Inflector::humanize($table_type));
	$controller = Inflector::pluralize($table_type);
	if(isset($group_id) && !empty($group_id) && $table_type == 'project')
	{
		$html->addCrumb('Groups', '/groups/index'); 
		$html->addCrumb($group_name, '/groups/dashboard/' . $group_id);
	}

	$html->addCrumb($type, '/' . $controller . '/index');
	$html->addCrumb(addslashes($name), '/' . $controller  . '/dashboard/' . $table_id); 
	$html->addCrumb('Notes', '/notes/' . $table_type . '/' . $table_id);
	$html->addCrumb('Add Article', '/notes/add/' . $table_type . '/' . $table_id);
?>
<div id="add-div"></div>
<script type="text/javascript">
	laboratree.context = <?php echo $javascript->object($context); ?>;
	laboratree.notes.makeAdd('add-div', '<?php echo $html->url('/notes/add/' . $table_type . '/' . $table_id); ?>');
</script>
