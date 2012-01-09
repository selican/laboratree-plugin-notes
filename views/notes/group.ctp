<?php
	$html->addCrumb('Group', '/groups/index');
	$html->addCrumb($group['Group']['name'], '/groups/dashboard/' . $group['Group']['id']);
	$html->addCrumb('Notes', '/notes/group/' . $group['Group']['id']);
?>
<div id="articles-div"></div>
<script type="text/javascript">
	laboratree.context = <?php echo $javascript->object($context); ?>;
	laboratree.notes.makeList('<?php echo addslashes($group['Group']['name']) . ' - Note Articles'; ?>', 'articles-div', '<?php echo $html->url('/notes/group/' . $group['Group']['id'] . '.json'); ?>');
</script>
