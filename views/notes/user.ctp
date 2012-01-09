<?php
	$html->addCrumb('User', '/users/index');
	$html->addCrumb($user['User']['name'], '/users/dashboard/' . $user['User']['id']);
	$html->addCrumb('Notes', '/notes/user/' . $user['User']['id']);
?>
<div id="articles-div"></div>
<script type="text/javascript">
	laboratree.context = <?php echo $javascript->object($context); ?>;
	laboratree.notes.makeList('<?php echo $user['User']['name'] . ' - Note Articles'; ?>', 'articles-div', '<?php echo $html->url('/notes/user/' . $user['User']['id'] . '.json'); ?>');
</script>
