<?php 

/**
 * LICENSE: ##LICENSE##
 *
 * @category   Anahita
 * @package    Plg_Contentfilter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Creates a hyperlink from the URLs
 *
 * @category   Anahita
 * @package    Plg_Contentfilter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class PlgContentfilterLink extends PlgContentfilterAbstract
{
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
            'priority'   => KCommand::PRIORITY_LOW,
        ));

        parent::_initialize($config);
    }
    	
	/**
	 * Filter a value. 
	 * The code is extracted from the ignite framework auto_link 
	 * 
	 * @param string The text to filter
	 * 
	 * @return string
	 */	
	public function filter($text)
	{
		$matches = array();
		
		if (preg_match_all("#(^|\s|\()((http(s?)://)|(www\.))(\w+[^\s\)\<]+)#i", $text, $matches))
		{
			$pop = " target=\"_blank\" ";

			for ($i = 0; $i < count($matches['0']); $i++)
			{
				$period = '';
				if (preg_match("|\.$|", $matches['6'][$i]))
				{
					$period = '.';
					$matches['6'][$i] = substr($matches['6'][$i], 0, -1);
				}

				$text = str_replace($matches['0'][$i],
									$matches['1'][$i].'<a class="an-link" href="http'.
									$matches['4'][$i].'://'.
									$matches['5'][$i].
									$matches['6'][$i].'"'.$pop.'>http'.
									$matches['4'][$i].'://'.
									$matches['5'][$i].
									$matches['6'][$i].'</a>'.
									$period, $text);
			}
		}
		return $text;	
	}
}

?>