<?php
	$html->addCrumb('Groups', '/groups/index'); 
	$html->addCrumb($group_name, '/groups/dashboard/' . $group_id);
	$html->addCrumb('Project', '/projects/group/' . $group_id);
	$html->addCrumb($project['Project']['name'], '/projects/dashboard/' . $project['Project']['id']);
	$html->addCrumb('Notes', '/notes/project/' . $project['Project']['id']);
?>
<div id="articles-div"></div>
<script type="text/javascript">
	laboratree.context = <?php echo $javascript->object($context); ?>;
	laboratree.notes.makeList('<?php echo $project['Project']['name'] . ' - Note Articles'; ?>', 'articles-div', '<?php echo $html->url('/notes/project/' . $project['Project']['id'] . '.json'); ?>');
</script>
