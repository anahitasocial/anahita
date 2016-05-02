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
              'about' => array(
                  'label' => 'COM-SETTINGS-ABOUT',
                  'url' => 'option=com_settings&view=about'
              ),
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
            'description' => '',
            'disabled' => false,
            'type' => 'text',
            'pattern' => '*',
            'required' => true,
        ));

        return $this->_render('formfield_text', $config);
    }

    /**
    *   renders a textarea form field
    *
    *   @param array attributes
    *   @return html form field
    */
    public function formfield_textarea($config = array())
    {
        $config = new KConfig($config);

        $config->append(array(
            'class' => 'input-block-level',
            'maxlength' => 1000,
            'name' => '',
            'id' => '',
            'label' => '',
            'placeholder' => '',
            'description' => '',
            'disabled' => false,
            'required' => true,
            'rows' => 5,
            'cols' => 3,
        ));

        return $this->_render('formfield_textarea', $config);
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
            'description' => '',
            'id' => '',
            'label' => '',
            'options' => array(),
            'disabled' => false,
        ));

        return $this->_render('formfield_select', $config);
    }

    public function formfield_custom($config)
    {
      $config = new KConfig($config);

      $config->append(array(
          'class' => 'input-block-level'
      ));

       return $this->_render('formfield_custom', $config);
    }

    public function params($config = array())
    {
        $config = new KConfig($config);

        $config->append(array(
            'type' => 'component',
            'package' => $config->entity->option,
        ));

        $entity = $config->entity;
        $package = $entity->option;
        $config_file_path = JPATH_SITE.DS.'components'.DS.$package.DS.'config.json';

        if(!file_exists($config_file_path)) {
           return JText::_('COM-SETTINGS-PROMPT-NO-CONFIGURATION-AVAILABLE');
        }

        $html = '';
        JFactory::getLanguage()->load($package);
        $app_config = json_decode(file_get_contents($config_file_path));

        foreach ($app_config->fields as $field) {

            switch($field->type){

                case 'text' :
                    $html .= $this->formfield_text(array(
                        'name' => $field->name,
                        'id' => 'param-'.$field->name,
                        'label' => JText::_($field->label),
                        'placeholder' => isset($field->placeholder) ? JText::_($field->placeholder) : '',
                        'description' => isset($field->description) ? JText::_($field->description) : '',
                        'maxlength' => isset($field->size) ? $field->size : 200,
                        'value' => $entity->getValue($field->name, $field->default),
                        'disabled' => isset($field->disabled) ? $field->disabled : 0,
                        'required' => isset($field->required) ? $field->required : 0,
                    ));
                break;

                case 'list' :
                case 'radio':

                   $options = array();
                   foreach($field->option as $i=>$option){
                     $options[] = array(
                        'name' => $option->text,
                        'value' => $option->value,
                     );
                   }

                   $value = $entity->getValue($field->name);

                   $html .= $this->formfield_select(array(
                     'name' => $field->name,
                     'id' => 'param-'.$field->name,
                     'label' => JText::_($field->label),
                     'selected' => ($value === '') ? $field->default : $value,
                     'description' => isset($field->description) ? JText::_($field->description) : '',
                     'options' => $options,
                     'disabled' => isset($field->disabled) ? 1 : 0,
                   ));

                break;

                case 'textarea' :
                    $html .= $this->formfield_textarea(array(
                        'name' => $field->name,
                        'id' => 'param-'.$field->name,
                        'label' => JText::_($field->label),
                        'placeholder' => isset($field->placeholder) ? JText::_($field->placeholder) : '',
                        'description' => isset($field->description) ? JText::_($field->description) : '',
                        'maxlength' => isset($field->size) ? $field->size : 200,
                        'value' => html_entity_decode($entity->getValue($field->name)),
                        'disabled' => isset($field->disabled) ? 1 : 0,
                        'cols' => $field->cols,
                        'rows' => $field->rows,
                    ));
                break;

                case 'legend' :
                  $html .= "<legend>".$field->default."</legend>\n\n";
                break;

                case 'custom' :

                   $value = $entity->getValue($field->name);

                   $html .= $this->formfield_custom(array(
                     'name' => $field->name,
                     'id' => 'param-'.$field->name,
                     'label' => JText::_($field->label),
                     'value' => ($value === '') ? $field->default : $value,
                     'disabled' => isset($field->disabled) ? 1 : 0,
                     'identifier' => $field->identifier,
                     'placeholder' => isset($field->placeholder) ? JText::_($field->placeholder) : '',
                     'description' => isset($field->description) ? JText::_($field->description) : '',
                   ));
                break;
            }
        }

        return $html;
    }
}
