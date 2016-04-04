<?php

/**
 * Settings UI Helper
 *
 * Helper methods for ui elements
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComSettingsTemplateHelperUi extends ComBaseTemplateHelperUi
{
    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
          'paths' => array(dirname(__FILE__).'/ui'),
        ));

        parent::_initialize($config);

        $paths = KConfig::unbox($config->paths);
        array_unshift($paths, JPATH_THEMES.'/'.JFactory::getApplication()->getTemplate().'/html/com_settings/ui');
        $config->paths = $paths;
    }

    public function navigation($config = array())
    {
        $config = array_merge_recursive($config, array(
          'tabs' => array(
              'configurations' => array(
                  'label' => 'COM-SETTINGS-CONFIGURATIONS',
                  'url' => '#'
              ),
              'apps' => array(
                  'label' => 'COM-SETTINGS-APPS',
                  'url' => '#'
              ),
              'plugins' => array(
                  'label' => 'COM-SETTINGS-PLUGINS',
                  'url' => '#'
              ),
              'templates' => array(
                  'label' => 'COM-SETTINGS-TEMPLATES',
                  'url' => '#'
              ),
              'about' => array(
                  'label' => 'COM-SETTINGS-ABOUT',
                  'url' => '#'
              ),
          ),
          'selected' => 'configurations'
        ));

        return $this->_render('navigation', $config);
    }
}
