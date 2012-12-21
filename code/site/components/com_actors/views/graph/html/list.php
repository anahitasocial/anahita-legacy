<?php defined('KOOWA') or die; ?>

<?php 
if ( $item->isAdministrable() )
	//set the actor as state
    @listItemView()->getState()->actor = $item;        
?>

<?= @previous() ?>  

