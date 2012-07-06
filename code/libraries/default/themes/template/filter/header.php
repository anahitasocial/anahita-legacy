<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Themes
 * @subpackage Filter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Renders header
 * 
 * @category   Anahita
 * @package    Lib_Themes
 * @subpackage Filter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibThemesTemplateFilterHeader extends KTemplateFilterAbstract implements KTemplateFilterWrite
{	
    /**
     * Convert the alias
     *
     * @param string
     * @return KTemplateFilterAlias
     */
    public function write(&$text) 
    {
    	$matches  = array();

        if ( strpos($text, '<jdoc:include type="script" />') ) {
        	//renders the script
    		$text = str_replace('<jdoc:include type="script" />', $this->renderScripts() , $text);
    		
    		//empty the script to avoid being rendered twince
    		$document = $this->_template->getDocument();		
			$document->_scripts = array();
			$document->_script  = array();	
					
    	}
    	    	
    	if ( strpos($text, '<jdoc:include type="head" />') ) {
    		$text = str_replace('<jdoc:include type="head" />', $this->renderHeader() , $text);
    	}
    }       
    
    /**
     * Get the document header string
     *      
     * @return string
     */
    public function renderHeader()
    {    	
    	$document = $this->_template->getDocument();
    	
		// get line endings
		$lnEnd = $document->_getLineEnd();
		$tab = $document->_getTab();

		$tagEnd	= ' />';

		$strHtml = '';

		// Generate base tag (need to happen first)
		$base = $document->getBase();
		if(!empty($base)) {
			$strHtml .= $tab.'<base href="'.$document->getBase().'" />'.$lnEnd;
		}

		// Generate META tags (needs to happen as early as possible in the head)
		foreach ($document->_metaTags as $type => $tag)
		{
			foreach ($tag as $name => $content)
			{
				if ($type == 'http-equiv') {
					$strHtml .= $tab.'<meta http-equiv="'.$name.'" content="'.$content.'"'.$tagEnd.$lnEnd;
				} elseif ($type == 'standard') {
					$strHtml .= $tab.'<meta name="'.$name.'" content="'.str_replace('"',"'",$content).'"'.$tagEnd.$lnEnd;
				}
			}
		}

		$strHtml .= $tab.'<meta name="description" content="'.$document->getDescription().'" />'.$lnEnd;
		$strHtml .= $tab.'<meta name="generator" content="'.$document->getGenerator().'" />'.$lnEnd;

		$strHtml .= $tab.'<title>'.htmlspecialchars($document->getTitle()).'</title>'.$lnEnd;

		// Generate link declarations
		foreach ($document->_links as $link) {
			$strHtml .= $tab.$link.$tagEnd.$lnEnd;
		}

		$strHtml .= $this->renderStyles();
		$strHtml .= $this->renderScripts();

		foreach($document->_custom as $custom) {
			$strHtml .= $tab.$custom.$lnEnd;
		}

		return $strHtml;    	
    }
     
 	/**
     * Return the scripts from the document
     * 
     * @return string
     */
    public function renderScripts()
    {
    	$document = $this->_template->getDocument();
    	$tab 	  = $document->_getTab();
    	$lnEnd 	  = $document->_getLineEnd();
    	$tagEnd   = ' />';
    	$strHtml  = '';
  	  	// Generate script file links
		foreach ($document->_scripts as $strSrc => $strType) {
			$strHtml .= $tab.'<script type="'.$strType.'" src="'.$strSrc.'"></script>'.$lnEnd;
		}

		// Generate script declarations
		foreach ($document->_script as $type => $content)
		{
			$strHtml .= $tab.'<script type="'.$type.'">'.$lnEnd;

			// This is for full XHTML support.
			if ($document->_mime != 'text/html' ) {
				$strHtml .= $tab.$tab.'<![CDATA['.$lnEnd;
			}

			$strHtml .= $content.$lnEnd;

			// See above note
			if ($document->_mime != 'text/html' ) {
				$strHtml .= $tab.$tab.'// ]]>'.$lnEnd;
			}
			$strHtml .= $tab.'</script>'.$lnEnd;
		}
		
		return   $strHtml;  	
    }
    
 	/**
     * Return the styles from the document
     * 
     * @return string
     */    
    public function renderStyles()
    {
    	$document = $this->_template->getDocument();
    	$tab 	  = $document->_getTab();
    	$lnEnd 	  = $document->_getLineEnd();
    	$tagEnd	= ' />';
    	$strHtml  = '';
    	    	
    	// Generate stylesheet links
		foreach ($document->_styleSheets as $strSrc => $strAttr )
		{
			$rel 	= 'stylesheet';
			if ( strpos($strSrc, '.less') ) {
				$rel .= '/less';
			}
			$strHtml .= $tab . '<link rel="'.$rel.'" href="'.$strSrc.'" type="'.$strAttr['mime'].'"';
			if (!is_null($strAttr['media'])){
				$strHtml .= ' media="'.$strAttr['media'].'" ';
			}
			if ($temp = JArrayHelper::toString($strAttr['attribs'])) {
				$strHtml .= ' '.$temp;;
			}
			$strHtml .= $tagEnd.$lnEnd;
		}

		// Generate stylesheet declarations
		foreach ($document->_style as $type => $content)
		{
			$strHtml .= $tab.'<style type="'.$type.'">'.$lnEnd;

			// This is for full XHTML support.
			if ($document->_mime == 'text/html' ) {
				$strHtml .= $tab.$tab.'<!--'.$lnEnd;
			} else {
				$strHtml .= $tab.$tab.'<![CDATA['.$lnEnd;
			}

			$strHtml .= $content . $lnEnd;

			// See above note
			if ($document->_mime == 'text/html' ) {
				$strHtml .= $tab.$tab.'-->'.$lnEnd;
			} else {
				$strHtml .= $tab.$tab.']]>'.$lnEnd;
			}
			$strHtml .= $tab.'</style>'.$lnEnd;
		} 
				  
		return $strHtml; 	
    }
}