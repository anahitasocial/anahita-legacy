<?php defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?php 	
	$view = @view('story')->layout('list');
	if ( isset($actor) ) {
		$view->actor($actor);
	}
?>

<div id="an-stories" class="an-entities an-stories" >
<?php foreach($stories as $story) : ?>
	<?= $view->entity($story) ?>
<?php endforeach; ?>
</div>

<div id="an-more-records" class="an-more-records">
	<?php 
		$url = array('option'=>'com_stories', 'view'=>'stories', 'layout'=>'list');
        
        if(isset($filter))
        	$url['filter'] = $filter;
        elseif (isset($actor))
        	$url['oid'] = $actor->id;
    ?>
	<?= @pagination($stories, array('merge_query'=>false,'url'=>@route($url))) ?>
</div>

<?php if(count($stories) == 0) :?>
<?= @message(@text('LIB-AN-PROMPT-NO-MORE-RECORDS-AVAILABLE')) ?>
<?php endif; ?>
