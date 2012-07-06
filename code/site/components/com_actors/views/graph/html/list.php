<?php defined('KOOWA') or die; ?>

<?php
$config = array(
        'entities'  => $entities,         
        'pagination'=> @pagination($entities, array('url'=>@route('layout=list&get='.$get.'&type='.$type.'&view='.@listItemView()->getName().'&id='.$entity->id)))        
    );
if ( $entity->isAdministrable() )
   $config['options'] = array('actor'=>$entity);
?>
<?= @previous($config) ?>
