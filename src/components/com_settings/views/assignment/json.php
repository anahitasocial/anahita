<?php 
/** 
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2020 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.Json>
 *
 * @link       http://www.Anahita.io
 */

class ComSettingsViewAssignmentJson extends ComBaseViewJson
{    
    public function display()
    {                
        $data = array(
            /* 
                This isn't an entity id, we are naming the identifier, id, 
                so the client side application can recognize the output
                as a list of entities.  
            */
            'id' => sprintf('com:%s.domain.entity.%s', $this->actor->package, $this->actor->name),
            'apps' => array(),
        );
        
        foreach($this->apps as $j => $app) {
            $selected = $app->getAssignmentForIdentifier($this->actor);
            $isOptional = $app->getAssignmentOption() == ComComponentsDomainBehaviorAssignable::OPTION_OPTIONAL;
            $options = array();
            if ($isOptional) {
                $options[0] = 'optional';
            }
            $options[1] = 'always';
            $options[2] = 'never';
            
            $data['apps'][$j] = array(
                'id' => $app->id,
                'name' => $app->name,
                'options' => $options,
                'selected' => $options[$selected],
                'access' => $selected,
                'is_optional' => $isOptional,
            );
        }
        
        $this->output = json_encode($data);
        
        if (! empty($this->_padding)) {
            $this->output = $this->_padding.'('.$this->output.');';
        }
        
        return $this->output;
    }
}