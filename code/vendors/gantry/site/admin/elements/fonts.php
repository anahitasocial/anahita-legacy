<?php
/**
 * @package     gantry
 * @subpackage  admin.elements
 * @version		3.1.4 November 12, 2010
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
defined('JPATH_BASE') or die();
/**
 * @package     gantry
 * @subpackage  admin.elements
 */
class JElementFonts extends JElement {

    var $_google_fonts = array("Aclonica", "Allan", "Allerta", "Allerta Stencil", "Amaranth", "Annie Use Your Telescope", "Anonymous Pro", "Anton", "Architects Daughter", "Arimo", "Artifika", "Arvo", "Asset", "Astloch", "Bangers", "Bentham", "Bevan", "Bigshot One", "Brawler", "Buda", "Cabin", "Cabin Sketch", "Calligraffitti", "Candal", "Cantarell", "Cardo", "Carter One", "Caudex", "Cedarville Cursive", "Cherry Cream Soda", "Chewy", "Coda", "Coming Soon", "Copse", "Corben", "Cousine", "Covered By Your Grace", "Crafty Girls", "Crimson Text", "Crushed", "Cuprum", "Damion", "Dancing Script", "Dawning of a New Day", "Didact Gothic", "Droid Sans", "Droid Sans Mono", "Droid Serif", "EB Garamond", "Expletus Sans", "Fontdiner Swanky", "Francois One", "Geo", "Goblin One", "Goudy Bookletter 1911", "Gravitas One", "Gruppo", "Hammersmith One", "Holtwood One SC", "Homemade Apple", "IM Fell", "Inconsolata", "Indie Flower", "Irish Grover", "Josefin Sans", "Josefin Slab", "Judson", "Jura", "Just Another Hand", "Just Me Again Down Here", "Kameron", "Kenia", "Kranky", "Kreon", "Kristi", "La Belle Aurore", "Lato", "League Script", "Lekton", "Limelight", "Lobster", "Lobster Two", "Lora", "Luckiest Guy", "Maiden Orange", "Mako", "Maven Pro", "Meddon", "MedievalSharp", "Megrim", "Merriweather", "Metrophobic", "Michroma", "Miltonian", "Molengo", "Monofett", "Mountains of Christmas", "Muli", "Neucha", "Neuton", "News Cycle", "Nixie One", "Nobile", "Nova", "Nunito", "OFL Sorts Mill Goudy TT", "Old Standard TT", "Open Sans", "Orbitron", "Oswald", "Over the Rainbow", "PT Sans", "PT Serif", "Pacifico", "Paytone One", "Permanent Marker", "Philosopher", "Play", "Playfair Display", "Podkova", "Puritan", "Quattrocento", "Quattrocento Sans", "Radley", "Raleway", "Redressed", "Reenie Beanie", "Rock Salt", "Rokkitt", "Ruslan Display", "Schoolbell", "Shadows Into Light", "Shanti", "Sigmar One", "Six Caps", "Slackey", "Smythe", "Sniglet", "Special Elite", "Sue Ellen Francisco", "Sunshiney", "Swanky and Moo Moo", "Syncopate", "Tangerine", "Tenor Sans", "Terminal Dosis Light", "The Girl Next Door", "Tinos", "Ubuntu", "Ultra", "UnifrakturCook", "UnifrakturMaguntia", "Unkempt", "VT323", "Varela", "Vibur", "Vollkorn", "Waiting for the Sunrise", "Wallpoet", "Walter Turncoat", "Wire One", "Yanone Kaffeesatz", "Zeyada");


	function fetchElement($name, $value, &$node, $control_name, $options = false, $translation = true) {
        global $gantry;

		if (!defined('GANTRY_SELECTBOX')) {
			$this->template = end(explode(DS, $gantry->templatePath));
			gantry_addScript($gantry->gantryUrl.'/admin/widgets/selectbox/js/selectbox.js');

			define('GANTRY_SELECTBOX', 1);
		}

		$lis = $activeElement = "";
        $options_list = "";
		$xml = false;

		if (!$options) {
			$options = $node->children();
			$xml = true;
		}

		$isPreset = ($node->attributes('preset')) ? $node->attributes('preset') : false;



		foreach($options as $option) {
			if ($xml) {
				$optionData = $option->data();
				$optionValue = $option->attributes('value');
				$optionDisabled = $option->attributes('disable');
			} else {
				$optionData = $option->text;
				$optionValue = $option->value;
				$optionDisabled = $option->disable;
			}

			$disabled = ($optionDisabled == 'disable') ? "disabled='disabled'" : "";
			$selected = ($value == $optionValue) ? "selected='selected'" : "";
			$active = ($value == $optionValue) ? "class='active'" : "";
			if (strlen($active)) $activeElement = $optionData;

			if (strlen($disabled)) $active = "class='disabled'";

			$imapreset = ($isPreset) ? "im-a-preset" : "";

			$text = ($translation) ? JTEXT::_($optionData) : $optionData;

			$options_list .= "<option value='$optionValue' $selected $disabled>".$text."</option>\n";
			$lis .= "<li ".$active.">".$text."</li>";
		}

        // add webfonts if enabled
        if ($gantry->get('webfonts-enabled')) {
            // only google right now
            if ($gantry->get('webfonts-source') == 'google') {
                $webfonts = $this->_google_fonts;
            }
            foreach ($webfonts as $webfont) {
                $webfontsData = $webfont;
				$webfontsValue = $webfont;

			    $selected = ($value == $webfontsValue) ? "selected='selected'" : "";
			    $active = ($value == $webfontsValue) ? "class='active'" : "";
				if (strlen($active)) $activeElement = $webfontsData;
                $text = $webfontsData;

                $options_list .= "<option value='$webfontsValue' $selected>".$text."</option>\n";
			    $lis .= "<li ".$active.">".$text."</li>";

            }
        }

		$html  = "<div class='wrapper'>";
		$html .= "<div class='selectbox-wrapper'>";

		$html .= "	<div class='selectbox'>";

		$html .= "		<div class='selectbox-top'>";
		$html .= "			<div class='selected'><span>".JTEXT::_($activeElement)."</span></div>";
		$html .= "			<div class='arrow'></div>";
		$html .= "		</div>";
		$html .= "		<div class='selectbox-dropdown'>";
		$html .= "			<ul>".$lis."</ul>";
		$html .= "			<div class='selectbox-dropdown-bottom'><div class='selectbox-dropdown-bottomright'></div></div>";
		$html .= "		</div>";

		$html .= "	</div>";

		$html .= "	<select id='params".$name."' name='params[".$name."]' class='selectbox-real ".$imapreset."'>";
		$html .= 		$options_list;
		$html .= "	</select>";
		$html .= "</div>";
		$html .= "<div class='clr'></div>";
		$html .= "</div>";

		return $html;
	}

}

?>