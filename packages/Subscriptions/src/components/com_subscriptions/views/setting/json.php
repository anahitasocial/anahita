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
class ComSubscriptionsViewSettingJson extends LibBaseViewJson {
    
    public function display()
    {
        $data = array(
            'name' => 'subscription',
            'description' => 'This api will be discontinued soon!',
        );
        
        return json_encode($data);
    }
}
