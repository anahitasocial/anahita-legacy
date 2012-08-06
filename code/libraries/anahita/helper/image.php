<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Anahita_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Image Helper
 *
 * @category   Anahita
 * @package    Anahita_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnHelperImage extends KObject
{
	/**
	 * Resizes an image based on the options passed
	 * 
	 * @param $image resource
	 * @param $size string 
	 * @param $options array[Optional]
	 * @return string data
	 */
	function resize( $image, $size , $options=array())
	{
		
		$options = array_merge(array('format'=>null), $options);
		
		if ( !$image ) 
			return false;
		
		$height = null;
		$width  = null;
		
		if ( strpos($size,'x') || count((array) $size) == 2 ) {
			list($width,$height) = is_array($size) ? $size : explode('x',$size); 
		} else {
			$size =  (array) $size;
			$width = (int) $size[0];
		}
		

		if ($height == 'auto' && $width == 'auto' )
			return false;
		
		$o_wd = imagesx($image);
		$o_ht = imagesy($image);
	
		$x  = $y  = 0;
		if( $height == 'auto' )
			$height = round( ( $width / $o_wd ) * $o_ht );
		else if ( $width == 'auto' )
			$width = round( ( $height / $o_ht ) * $o_wd );
		else if ($width && !$height) {
			//make square image
			$height = $width;	
			if($o_wd> $o_ht) {
				$x = ceil(($width - $height) / 2 );
				$o_wd = $o_ht;
			} elseif($o_ht> $o_wd) {
				$y = ceil(($height - $width) / 2);
				$o_ht = $o_wd;
			}		
		} else 
		{
			$w = round($o_wd * $height / $o_ht);
			$h = round($o_ht * $width / $o_wd);
		
			if( ($height-$h) < ($width-$w) )
				$width =& $w;
			else
				$height =& $h;
		}
		
		$tmp = imageCreateTrueColor( $width, $height );

		imagecopyresampled($tmp, $image, 0, 0, $x, $y, $width, $height, $o_wd, $o_ht);
		
		if ( $options['format'] == null ) 
			return $tmp;
		//
		if ( !isset($options['format']) || !in_array($options['format'],array('jpeg','jpg','png','gif'))) {		
			throw new Exception("Invalid Image Type");
		}
		
		if  ( $options['format'] == 'jpg' ) {
			$options['format'] = 'jpeg';
		}
		
		$func = 'image'.strtolower($options['format']);
		ob_start();	
		$func($tmp, NULL, 100);
		$tmp = ob_get_clean();
		return $tmp;
	}
}