<?php defined('KOOWA') or die; ?>

<?php 
    $url = $item->getURL().'&layout=list&get=graph&type='.$type;
    if ( !empty($q) ) {
        $url .= '&q='.$q;   
    }
?>
<?php
    $config = array(              
        'pagination'=> @pagination($this->getView()->getState()->getList(), array('url'=>@route($url)))        
    );
if ( $item->isAdministrable() && false )
{
   $config['options'] = array('actor'=>$item);   
}
 
?>
<?= @previous($config) ?>  

