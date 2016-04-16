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
        $config = array_merge($config, array(
          'tabs' => array(
              'settings' => array(
                  'label' => 'COM-SETTINGS-SYSTEM',
                  'url' => 'option=com_settings&view=settings'
              ),
              'actors' => array(
                  'label' => 'COM-SETTINGS-ACTORS',
                  'url' => 'option=com_settings&view=actors'
              ),
              'apps' => array(
                  'label' => 'COM-SETTINGS-APPS',
                  'url' => 'option=com_settings&view=apps'
              ),
              'plugins' => array(
                  'label' => 'COM-SETTINGS-PLUGINS',
                  'url' => 'option=com_settings&view=plugins'
              ),
              'templates' => array(
                  'label' => 'COM-SETTINGS-TEMPLATES',
                  'url' => 'option=com_settings&view=templates'
              ),
              'about' => array(
                  'label' => 'COM-SETTINGS-ABOUT',
                  'url' => 'option=com_settings&view=about'
              ),
          ),
        ));

        if(!isset($config['selected'])) {
          $config['selected'] = 'settings';
        }

        return $this->_render('navigation', $config);
    }

    /**
    *   renders a text form field
    *
    *   @param array attributes
    *   @return html form field
    */
    public function formfield_text($config = array())
    {
        $config = new KConfig($config);

        $config->append(array(
            'class' => 'input-block-level',
            'maxlength' => 200,
            'name' => '',
            'id' => '',
            'label' => '',
            'placeholder' => '',
            'disabled' => false,
            'pattern' => '',
        ));

        return $this->_render('formfield_text', $config);
    }

    /**
    *   renders a select form field
    *
    *   @param array attributes
    *   @return html form field
    */
    public function formfield_select($config = array())
    {
        $config = new KConfig($config);

        $config->append(array(
            'class' => 'input-block-level',
            'name' => '',
            'selected' => '',
            'id' => '',
            'label' => '',
            'options' => array(),
            'disabled' => false,
        ));

        return $this->_render('formfield_select', $config);
    }
}
