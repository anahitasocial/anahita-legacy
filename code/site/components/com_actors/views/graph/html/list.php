<?php defined('KOOWA') or die; ?>

<?php
    $config = array(              
        'pagination'=> @pagination($this->getView()->getState()->getList(), array('url'=>@route('layout=list&get=graph&type='.$type.'&view='.@listItemView()->getName().'&id='.$item->id)))        
    );
if ( $item->isAdministrable() && false )
{
   $config['options'] = array('actor'=>$item);   
}
 
?>
<?= @previous($config) ?>  

