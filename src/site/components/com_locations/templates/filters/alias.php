<?php
 /**
  * Location Template Helper
  *
  * Provides alias to render maps
  *
  * @category   Anahita
  *
  * @author     Rastin Mehr <rastin@anahitapolis.com>
  * @copyright  2015 rmdStudio Inc.
  * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
  *
  * @link       http://www.GetAnahita.com
  */
class ComLocationsTemplateFilterAlias extends ComBaseTemplateFilterAlias
{
    /**
     * Constructor.
     *
     * @param 	object 	An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->_alias_read = array_merge($this->_alias_read, array(
            '@map(' => '$this->renderHelper(\'ui.map\','
        ));
    }
}
