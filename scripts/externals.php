<?php 
if ( !defined('DS') ) {
    define('DS', DIRECTORY_SEPARATOR );   
}

$root_path   = realpath(dirname(__FILE__).'/../code');
$joomla_path = realpath(dirname(__FILE__).'/../code/vendors/joomla');
$externals   = array($root_path.'/vendors/nooku', $root_path);

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

function get_paths($src)
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
		$symlinks[$path] = trim(str_replace('site/','', str_replace($src,'',$path)),'/');
	}
	return $symlinks;
}

$paths = array();
foreach(get_paths($root_path.'/vendors/nooku') as $from => $to)
{
	$from = str_replace($root_path.'/vendors/nooku','../nooku',$from);
	$paths[$from] = $to;
	
}

foreach(get_paths($root_path) as $from => $to)
{
	$from = str_replace($root_path,'../..',$from);
	$paths[$from] = $to;
}
$paths['../mc/rt_missioncontrol_j15'] = 'administrator/templates/rt_missioncontrol_j15';
$paths['../mc/plg_system_missioncontrol/missioncontrol.php'] = 'plugins/system/missioncontrol.php';
$paths['../mc/plg_system_missioncontrol/missioncontrol.xml'] = 'plugins/system/missioncontrol.xml';  
$output = array();
foreach($paths as $from => $to)
{
	$output[] = $from.' '.$to;
}
file_put_contents(dirname(__FILE__).'/externals.txt', implode("\n", $output)."\n");
