<?php

/**
 * Alias Filter.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComBaseTemplateFilterAlias extends LibBaseTemplateFilterAlias
{
    /**
     * Constructor.
     *
     * @param 	object 	An optional AnConfig object with configuration options
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        $this->_alias_read = array_merge($this->_alias_read, array(
            '@avatar(' => '$this->renderHelper(\'com:actors.template.helper.avatar\',',
            '@cover(' => '$this->renderHelper(\'com:actors.template.helper.cover\',',
            '@name(' => '$this->renderHelper(\'com:actors.template.helper.name\',',
        ));
    }
}
