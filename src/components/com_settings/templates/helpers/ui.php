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
        array_unshift($paths, ANPATH_THEMES.'/'.$this->getService('application')->getTemplate().'/html/com_settings/ui');
        $config->paths = $paths;
    }

    public function sorting($config = array())
    {
        $config = new KConfig($config);

        $config->append(array(
          'label' => 'LIB-AN-SORT-'.strtoupper($config['field']),
          'field' => '',
          'url' => array()
        ));

        if($config->field) {
          $config->url['sort'] = $config->field;
        }

        return $this->_render('sorting', $config);
    }

    /**
    * Renders navigation bar for settings
    *
    * @param array of menu items
    * @param string html
    */
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
              'apps' => array(
                  'label' => 'COM-SETTINGS-APPS',
                  'url' => 'option=com_settings&view=apps'
              ),
              'assignments' => array(
                  'label' => 'COM-SETTINGS-ASSIGNMENTS',
                  'url' => 'option=com_settings&view=assignments'
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
    *   renders a list of plugin folders
    */
    public function plugin_types($config = array())
    {
        $config = new KConfig($config);

        $config->append(array(
          'selected' => '',
          'name' => 'folder',
          'label' => '',
          'params' => array()
        ));

        $query = KService::get('repos:settings.plugin')->getQuery();

        $query->order('name')->set('distinct', true);

        $config->folders = $query->fetchValues('folder');

        return $this->_render('plugin_types', $config);
    }

    /**
    *   renders a list of available templates
    */
    public function templates($config = array())
    {
          $config = new KConfig($config);

          $exclude = array(
            'base',
            'system'
          );

          $templates = preg_grep('/^([^.])/', scandir(ANPATH_THEMES));
          $templates = array_diff($templates, $exclude);

          $options = array();

          foreach($templates as $template){
              $options[] = array(
                'name' => AnInflector::humanize($template),
                'value' => $template
              );
          }

          $config->options = $options;

          return $this->formfield_select($config);
    }

    /**
    *   renders a list of available languages
    */
    public function languages($config = array())
    {
          $config = new KConfig($config);
          $path = ANPATH_SITE.DS.'language';
          $languages = preg_grep('/^([^.])/', scandir($path));
          $options = array();

          foreach($languages as $language){
              $manifest = json_decode(file_get_contents($path.DS.$language.DS.$language.'.json'));
              $options[] = array(
                'name' => $manifest->name,
                'value' => $language
              );
          }

          $config->options = $options;

          return $this->formfield_select($config);
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
            'pattern' => '',
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

    /**
    *  renders a custom developed form field
    *
    *   @param array attributes
    *   @return html form field
    */
    public function formfield_custom($config)
    {
        $config = new KConfig($config);

        $config->append(array(
          'class' => 'input-block-level'
        ));

        return $this->_render('formfield_custom', $config);
    }

    /**
    *  renders a component (app) or plugin paramters as form fields
    *
    *   @param array attributes
    *   @return html form fields
    */
    public function params($config = array())
    {
        $config = new KConfig($config);

        $config->append(array(
            'type' => 'component'
        ));

        $method = '_params'.ucfirst($config->type);
        return $this->$method($config);
    }

    /**
    *  renders a component's (app) paramters as form fields
    *
    *   @param array attributes
    *   @return html form fields
    */
    protected function _paramsComponent($config)
    {
        $entity = $config->entity;
        $package = $entity->option;
        $config_file_path = ANPATH_SITE.DS.'components'.DS.$package.DS.'config.json';

        if(!file_exists($config_file_path)) {
           return AnTranslator::_('COM-SETTINGS-PROMPT-NO-CONFIGURATION-AVAILABLE');
        }

        $this->getService('anahita:language')->load($package);
        $app_config = json_decode(file_get_contents($config_file_path));

        return $this->_renderForm($app_config->fields, $entity);
    }

    /**
    *  renders a plugin's paramters as form fields
    *
    *   @param array attributes
    *   @return html form fields
    */
    protected function _paramsPlugin($config)
    {
        $entity = $config->entity;
        $element = $entity->element;
        $folder = $entity->folder;

        $config_file_path = ANPATH_SITE.DS.'plugins'.DS.$folder.DS.$element.'.json';

        if(file_exists($config_file_path)) {
            $plugin_config = json_decode(file_get_contents($config_file_path));
            if(isset($plugin_config->fields)) {
                return $this->_renderForm($plugin_config->fields, $entity);
            } else {
                return AnTranslator::_('COM-SETTINGS-PROMPT-NO-CONFIGURATION-AVAILABLE');
            }
        } else {
            return AnTranslator::_("Couldn't find the {$element}.json file!");
        }
    }

    /**
    *  renders a template's paramters as form fields
    *
    *   @param array attributes
    *   @return html form fields
    */
    protected function _paramsTemplate($config)
    {
        $entity = $config->entity;
        $template = $entity->alias;
        $config_file_path = ANPATH_THEMES.DS.$template.DS.'template.json';

        if(!file_exists($config_file_path)) {
           return AnTranslator::_('COM-SETTINGS-PROMPT-NO-CONFIGURATION-AVAILABLE');
        }

        $template_config = json_decode(file_get_contents($config_file_path));

        return $this->_renderForm($template_config->fields, $entity);
    }

    protected function _renderForm($fields, $entity)
    {
        $html = '';

        foreach ($fields as $field) {

            switch($field->type){

                case 'text' :
                    $html .= $this->formfield_text(array(
                        'name' => sprintf('meta[%s]', $field->name),
                        'id' => 'param-'.$field->name,
                        'label' => AnTranslator::_($field->label),
                        'placeholder' => isset($field->placeholder) ? AnTranslator::_($field->placeholder) : '',
                        'description' => isset($field->description) ? AnTranslator::_($field->description) : '',
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
                     'name' => sprintf('meta[%s]', $field->name),
                     'id' => 'param-'.$field->name,
                     'label' => AnTranslator::_($field->label),
                     'selected' => ($value === '') ? $field->default : $value,
                     'description' => isset($field->description) ? AnTranslator::_($field->description) : '',
                     'options' => $options,
                     'disabled' => isset($field->disabled) ? 1 : 0,
                   ));

                break;

                case 'textarea' :
                    $html .= $this->formfield_textarea(array(
                        'name' => sprintf('meta[%s]', $field->name),
                        'id' => 'param-'.$field->name,
                        'label' => AnTranslator::_($field->label),
                        'placeholder' => isset($field->placeholder) ? AnTranslator::_($field->placeholder) : '',
                        'description' => isset($field->description) ? AnTranslator::_($field->description) : '',
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
                     'name' => sprintf('meta[%s]', $field->name),
                     'id' => 'param-'.$field->name,
                     'label' => AnTranslator::_($field->label),
                     'value' => ($value === '') ? $field->default : $value,
                     'disabled' => isset($field->disabled) ? 1 : 0,
                     'identifier' => $field->identifier,
                     'placeholder' => isset($field->placeholder) ? AnTranslator::_($field->placeholder) : '',
                     'description' => isset($field->description) ? AnTranslator::_($field->description) : '',
                   ));
                break;
            }
        }

        return $html;
    }
}
