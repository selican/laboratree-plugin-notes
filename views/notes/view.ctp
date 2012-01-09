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
	$html->addCrumb('Notes', '/notes/articles/' . $article['Note']['table_type'] . '/' . $article['Note']['table_id']); 
	$html->addCrumb($article['Note']['title'], '/notes/view/' . $article['Note']['id']); 
?>
<div id="note-view-div"></div>
<script type="text/javascript">
	laboratree.context = <?php echo $javascript->object($context); ?>;
	laboratree.notes.makeView('note-view-div', '<?php echo $article['Note']['id']; ?>', '<?php echo $html->url('/notes/view/' . $article['Note']['id'] . '.json'); ?>'); 
</script>
