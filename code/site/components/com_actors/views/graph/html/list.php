<?php defined('KOOWA') or die; ?>

<?php 
$url = $item->getURL().'&layout=list&get=graph&type='.$type;
if ( !empty($q) ) {
    $url .= '&q='.$q;   
}
$config = array(              
    'pagination'=> @pagination($this->getView()->getState()->getList(), array('url'=>@route($url)))        
);
if ( $item->isAdministrable() ) {
     //set the actor as state
     @listItemView()->getState()->actor = $item;
}        
?>
<?= @previous($config) ?>  

