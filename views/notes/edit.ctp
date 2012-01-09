<?php
	
	$type = Inflector::pluralize(Inflector::humanize($article['Note']['table_type']));
	$controller = Inflector::pluralize($article['Note']['table_type']);
	if(isset($group_id) && !empty($group_id) && $article['Note']['table_type'] == 'project')
	{
		$html->addCrumb('Groups', '/groups/index'); 
		$html->addCrumb($group_name, '/groups/dashboard/' . $group_id);
	} 
	
 	$html->addCrumb($type, '/' . $controller . '/index');
	$html->addCrumb(addslashes($name), '/' . $controller . '/dashboard/' . $article['Note']['table_id']); 
	$html->addCrumb($article['Note']['title'], '/notes/' . $article['Note']['table_type'] . '/' . $article['Note']['table_id']);
	$html->addCrumb('Edit Article', '/notes/edit/' . $article['Note']['id']); 
?>
<div id="edit-div"></div>
<script type="text/javascript">
	laboratree.context = <?php echo $javascript->object($context); ?>;
	laboratree.notes.makeEdit('edit-div', '<?php echo $html->url('/notes/edit/' . $article['Note']['id']); ?>');
</script>
