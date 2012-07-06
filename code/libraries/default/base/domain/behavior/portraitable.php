<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Portraitable Behavior. 
 * 
 * An image representation of a node
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseDomainBehaviorPortraitable extends LibBaseDomainBehaviorStorable 
{
	/**
	 * Return an array of avatar sizes with its respective dimension
	 *  
	 * @return array 
	 */		
	static public function getDefaultSizes()
	{
		return array('small'=>'80xauto', 'medium' => '160xauto', 'large' => '480xauto', 'square' => 56);
	}
				
    /**
	 * Initializes the default configuration for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param KConfig $config An optional KConfig object with configuration options.
	 *
	 * @return void
	 */
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'attributes' 	=> array(
				'filename' => array('write'=>'protected'),
				'mimetype' => array('write'=>'protected')
			)
		));
		
		parent::_initialize($config);
	}
	
	/**
	 * Persist the data of a photo into the storage 
	 * 
	 * @param array $options An array of options
	 * 
	 * @return boolean
	 */
	public function setPortraitImage($options = array())
	{
		if ( $this->state() == AnDomain::STATE_NEW ) {
			$this->__image_options = $options;
			return $this;
		}
				
		$options = new KConfig($options);
		
		$options->append(array(
			'rotation' => 0,	
			'mimetype' => 'image/jpg'
		));

		if ( $options->url ) {
			$options->append(array(
				'data' => file_get_contents($options->url)
			));
		}
			
		$data	  = $options->data;
		
		if ( empty($data) )
		{
			$this->filename = null;
			$this->mimetype = null;
			return;
		}
		
		$rotation = $options->rotation;
	
		switch($rotation)
		{
			case 3:$rotation=180;break;
			case 6:$rotation=-90;break;
			case 8:$rotation=90 ;break;
			default :
				$rotation = 0;			
		}
	
		$image 	= imagecreatefromstring($data);
		
		if($rotation != 0 )
			$image = imagerotate($image, $rotation, 0);

		$context = new KCommandContext();
		
		$context->append(array(
			'filename'	=> md5($this->getIdentityId()),
			'mimetype'	=> $options->mimetype,
			'image' 	=> $image,			
			'sizes'		=> array()
		));
		
		//for now set the mimetype to image/jpg only
		//later we should support different image types
		$context['mimetype'] = 'image/jpg';
		
		if ( strpos($context->mimetype,'/') && strpos($context->filename,'.') === false )
		{
		    list($type, $ext)  =  explode('/',$context->mimetype);
		    $context->filename .= '.'.$ext;
		}
        
		$this->_mixer->execute('before.storeimage', $context);

		$this->_mixer->setData(array(
			'filename'  => $context->filename,
			'mimetype'  => $context->mimetype
		), AnDomain::ACCESS_PROTECTED);
		
		$sizes	 = KConfig::unbox($context->sizes);
		
		if ( empty($sizes) ) {
			$sizes = self::getDefaultSizes();
		}
						
		foreach($sizes as $size => $dimension )
		{
			$data = AnHelperImage::resize($image, $dimension, array('format'=>'jpg'));
			
			$filename = $this->_mixer->getPortraitFile($size);
			
			$this->_mixer->writeData($filename, $data);
		}
		
		$this->_mixer->setValue('sizes', $sizes);
		
		imagedestroy($image);
		
		return $this->_mixer;
	}

	/**
	 * Return if the portrait is set
	 * 
	 * @return boolean
	 */
	public function portraitSet()
	{
		return !empty($this->filename);
	}
	
	/**
	 * Removes the portrait image
	 * 
	 * @return void
	 */
	public function removePortraitImage()
	{
		$sizes   = $this->_mixer->getPortraitSizes();
		if ( empty($sizes) ) {
			$sizes = explode(' ','original large medium small thumbnail square');
		} else
			$sizes = array_keys($sizes);
				
		foreach($sizes as $size) {
			$file = $this->_mixer->getPortraitFile($size);
			$this->_mixer->deletePath($file);
		}
		$this->filename = null;
	}
	
	/**
	 * Return the portrait file for a size
	 * 
	 * @return string
	 */
	public function getPortraitFile($size)
	{
		if ( $this->mimetype )
			$ext = 'jpg';
		else
			$ext = 'jpg';
		
		$filename = md5($this->getIdentityId()).'_'.$size.'.'.$ext;
		
		return $filename;
	}
	
	/**
	 * Return the portrait file identifier
	 * 
	 * @return string
	 */
	public function getFileIdentifier()
	{
		return md5($this->getIdentityId());
	}
	
	/**
	 * Return the URL to the portrait
	 * 
	 * @return string
	 */
	public function getPortraitURL($size='square')
	{
		$filename =  $this->_mixer->getPortraitFile($size);
		$url = $this->getPathURL($filename, true);
		return $url;
	}
		
	/**
	 * Obtain the list of available sizes and dimensions for this photo
	 * 
	 * @return array of $size=>$dimension
	 */
	public function getPortraitSizes()
	{
		return $this->getValue('sizes', array());
	}
		
	/**
	 * Called after inserting the entity
	 *
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return void
	 */
	protected function _afterEntityInsert(KCommandContext $context)
	{
		if ( !empty($this->__image_options) ) 
			$this->_mixer->setPortraitImage($this->__image_options);
	}
	
	/**
	 * Delete a photo image from the storage. 
	 * 
	 * @param KCommandContext $context Context parameter
	 *  
	 * @return boolean
	 */
	protected function _beforeEntityDelete(KCommandContext $context)
	{
		$this->_mixer->removePortraitImage();
	}
}