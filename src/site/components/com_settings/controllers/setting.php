<?php

/**
 * System settings Controller.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008-2016 rmd Studio Inc.
 * @license    GNU GPLv3
 *
 * @link       http://www.GetAnahita.com
 */

class ComSettingsControllerSetting extends ComBaseControllerResource
{
    /**
    *   browse service
    *
    *  @param KCommandContext $context Context Parameter
    *  @return void
    */
    protected function _actionRead(KCommandContext $context)
    {

        $setting = new JConfig();
        $this->getView()->set('setting', $setting);
    }

    /**
    *   edit service
    *
    *  @param KCommandContext $context Context Parameter
    *  @return void
    */
    protected function _actionEdit(KCommandContext $context)
    {
        $settings = new JConfig();
        $data = $context->data;

        unset($data->secret);
        unset($data->dbtype);

        $strings = array(
          'sitename',
          'log_path',
          'tmp_path',
          'fromname',
          'sendmail',
          'smtpport',
          'smtpuser',
          'smtphost',
          'db',
          'user',
          'host',
        );

        $integers = array(
          'error_reporting',
          'sef_rewrite',
          'debug',
          'caching',
          'cachetime',
          'lifetime',
          'smtpauth',
        );

        $cmds = array(
          'cache_handler',
          'session_handler',
          'mailer',
          'smtpsecure',
        );

        $emails = array(
          'mailfrom'
        );

        foreach($data as $key=>$value){

          if(in_array($key, $strings)){
            $settings->$key = KRequest::get('post.'.$key, 'string');
          }

          if(in_array($key, $integers)){
            $settings->$key = KRequest::get('post.'.$key, 'int');
          }

          if(in_array($key, $cmds)){
            $settings->$key = KRequest::get('post.'.$key, 'cmd');
          }

          if(in_array($key, $emails)){
            $settings->$key = KRequest::get('post.'.$key, 'email');
          }
        }

        $config_file = JPATH_CONFIGURATION.DS.'configuration.php';

        if(file_exists($config_file)){

            chmod($config_file, 0644);

            $content = "<?php\nclass JConfig{\n";
            foreach($settings as $key=>$value) {
              if(!is_array($value)){
                $content .= "    var \$$key = '$value';\n";
              }
            }
            $content .= "}\n";

            try {
              file_put_contents($config_file, $content);
            } catch (Exception $e) {
                throw new \RuntimeException($e->getMessage());
            }

            $this->setMessage('COM-SETTINGS-PROMPT-SUCCESS', 'success');
        }
    }
}
