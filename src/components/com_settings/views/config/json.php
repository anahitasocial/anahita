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
    public function display()
    {
        $data = $this->config->get('_attributes');
    
        $data['debug'] = (bool) $data['debug'];
        $data['sef_rewrite'] = (bool) $data['sef_rewrite'];
        $data['error_reporting'] = (int) $data['error_reporting'];
        $data['smtpauth'] = (bool) $data['smtpauth'];
        $data['smtpport'] = (int) $data['smtpport'];
        
        $this->output = json_encode($data);
        
        if (! empty($this->_padding)) {
            $this->output = $this->_padding.'('.$this->output.');';
        }
        
        return $this->output;
    }
}