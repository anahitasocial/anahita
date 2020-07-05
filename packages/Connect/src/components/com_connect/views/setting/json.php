<?php

/**
 * JSON View Class.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComConnectViewSettingJson extends LibBaseViewJson {
    
    public function display()
    {
        $data = array(
            'apis' => array(),
        );
        
        $apis = $this->_state->apis;
        $sessions = $this->_state->sessions;
        $actor = $this->_state->actor;
        
        foreach($apis as $api) {
            $session = $sessions->find(array('api' => $api->getName()));

            if ($session && !$session->validateToken()) {
                $session->delete()->save();
                $session = null;
            }
            
            $data['apis'][] = array(
                'server' => $api->getName(),
                'hasSession' => (bool) $session,
                'oid' => $actor->uniqueAlias,
                'action' => (bool) $session ? 'delete' : 'accesstoken',
            );
        }
    
        return json_encode($data);
    }
}
