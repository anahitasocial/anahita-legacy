<?php 
if ( !defined('DS') ) {
    define('DS', DIRECTORY_SEPARATOR );   
}
function anahita_symlinker($src, $root)
{
    $paths = array(
        $src.DS.'vendors/mc/rt_missioncontrol_j15' => $root.DS.'administrator/templates/rt_missioncontrol_j15',            
    );
    
    $paths = get_paths($src.DS.'vendors/joomla',$root);
   
    $paths = array_merge($paths, get_paths($src.DS.'vendors/nooku',$root));
    $paths = array_merge($paths, get_paths($src,$root));
    
    foreach($paths as $source => $target)
    {
        if ( !is_link($target) ) {        
            //must remove the target first before
            //being able to symlink to it
            shell_exec("rm -rf $target");
        }
        
        //unlink the target
        if ( is_link($target) ) {
            unlink($target);    
        }
        
        //if source is not a directory and the directory doesn't exists
        //create the directory
        if ( !is_dir($source)  && !file_exists(dirname($target)) ) {
            mkdir(dirname($target), 0707, true);
        }
                
        shell_exec("ln -nsf $source $target");
    } 
    
    $deadlinks = explode("\n", trim(`find -L {$root} -type l -lname '*'`));
    foreach($deadlinks as $link) {
        @unlink($link);   
    }
}

function get_entries($paths, $config = array())
{
    $config = array_merge(array(
        'skips'     => array(),
        'files_only'=> false
    ), $config);
    
    settype($paths, 'array');
    
    $skips   = $config['skips'];
    settype($skips, 'array');
    $skips[] = 'index\.html';   
    foreach($skips as $i => $skip) {
        $skips[$i] = '('.$skip.')';
    }
    $skips   = implode('|',$skips); 
    $entries = array();    
    foreach($paths as $path)
    {
        if ( !file_exists($path) ) {
            continue;    
        }
        
        $dh   = @opendir( $path );        
        while( false !== ( $file = readdir( $dh ) ) ) 
        {
            if ( strpos($file,'.') === 0 ) {
                continue;    
            }                        
            if ( preg_match('/'.$skips.'/', $file) ) {
                continue;    
            }   
            $file      = $path.DS.$file;            
            if ( $config['files_only'] && is_dir($file) )
                continue;
            $entries[] = $file;
        }                
    }    
    return $entries;    
}

function get_paths($src,$target)
{
    $paths[] = 'administrator/components';
    $paths[] = 'administrator/includes';
    $paths[] = 'administrator/language/en-GB';
    $paths[] = 'administrator/modules';
    $paths[] = 'administrator/templates';

    $paths[] = 'site/components';
    $paths[] = 'site/modules';
    $paths[] = 'site/includes';
    $paths[] = 'site/language/en-GB';
    $paths[] = 'site/templates';
    $paths[] = 'cli';
    $paths[] = 'components';
    $paths[] = 'includes';
    $paths[] = 'language/en-GB';
    $paths[] = 'modules';
    $paths[] = 'libraries';
    $paths[] = 'templates';    
    $paths[] = 'media';    
    foreach($paths as $i => $path) {
        $paths[$i] = $src.DS.$path;
    }
    $paths = array_merge($paths, get_entries($src.DS.'plugins',array('skips'=>'tmp')));
    $paths = get_entries($paths);
    $symlinks = array();
    foreach($paths as $path) {        
        $symlinks[$path] = $target.str_replace('site/','', str_replace($src,'',$path));
    }
    return $symlinks;    
}

$args = $_SERVER['argv'];
array_shift($args);
global $code, $root;
$root = array_shift($args);
if ( empty($root) ) {
    $root = realpath(dirname(__FILE__).'/../../../../site');
    if ( !file_exists($root) ) {
        $root = null;
    }
}
if ( empty($root) ) {
    exit(0);
}
$target = $root; 
$src    = realpath(dirname(__FILE__).'/../code');

anahita_symlinker($src, $target);
