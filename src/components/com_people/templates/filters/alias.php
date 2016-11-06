<?php

/**
 * Alias Filter.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2016 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPeopleTemplateFilterAlias extends LibBaseTemplateFilterAlias
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
            '@flash_message(' => '$this->renderHelper(\'ui.flash\',',
            '@flash_message' => '$this->renderHelper(\'ui.flash\')',
            '@commands(' => '$this->getHelper(\'toolbar\')->commands(',
            '@content(' => 'PlgContentfilterChain::getInstance()->filter(',
            '@pagination(' => '$this->renderHelper(\'ui.pagination\',',
            '@infinitescroll(' => '$this->renderHelper(\'ui.infinitescroll\',',
            '@avatar(' => '$this->renderHelper(\'com:actors.template.helper.avatar\',',
            '@cover(' => '$this->renderHelper(\'com:actors.template.helper.cover\',',
            '@name(' => '$this->renderHelper(\'com:actors.template.helper.name\',',
            '@editor(' => '$this->renderHelper(\'ui.editor\',',
            '@message(' => '$this->renderHelper(\'ui.message\',',
            '@date(' => '$this->renderHelper(\'date.format\',',
            '@searchbox(' => '$this->renderHelper(\'ui.search\',',
            '@location(' => '$this->renderHelper(\'com:locations.template.helper.ui.location\',',
            '@map(' => '$this->renderHelper(\'com:locations.template.helper.ui.map\',',
            '@map_api(' => '$this->renderHelper(\'com:locations.template.helper.ui.api\',',
            '@map_api_nearby(' => '$this->renderHelper(\'com:locations.template.helper.ui.nearby\',',
        ));
    }
}
