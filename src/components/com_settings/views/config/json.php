<?php 
/** 
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2020 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.Json>
 *
 * @link       http://www.GetAnahita.com
 */

class ComSettingsViewConfigJson extends ComBaseViewJson
{
    const _OMMITTED_FIELDS = array(
        'log_path',
        'tmp_path',
        'secret',
        'debug',
        'error_reporting',
        'dbtype',
        'host',
        'user',
        'password',
        'db',
        'dbprefix',
        'cors_enabled',
        'cors_methods',
        'cors_headers',
        'cors_credentials',
        'sef_rewrite',
    );
    
    public function display()
    {
        $data = $this->config->get('_attributes');
    
        foreach(self::_OMMITTED_FIELDS as $key) {
            if (array_key_exists($key, $data)) {
                unset($data[$key]);
            }
        } 

        $data['smtp_port'] = (int) $data['smtp_port'];
        
        $this->output = json_encode($data);
        
        if (! empty($this->_padding)) {
            $this->output = $this->_padding.'('.$this->output.');';
        }
        
        return $this->output;
    }
}