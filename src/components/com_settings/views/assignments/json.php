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

class ComSettingsViewAssignmentsJson extends ComBaseViewJson
{    
    public function display()
    {                
        $data = array();
        
        foreach($this->actors as $i => $actor) {
            $data[$i] = array(
                /* 
                    This isn't an entity id, we are naming the identifier, id, 
                    so the client side application can recognize the output
                    as a list of entities.  
                */
                'id' => sprintf('com:%s.domain.entity.%s', $actor->package, $actor->name),
                'apps' => array(),
            );
            
            foreach($this->apps as $j => $app) {
                $selected = $app->getAssignmentForIdentifier($actor);
                $isOptional = $app->getAssignmentOption() == ComComponentsDomainBehaviorAssignable::OPTION_OPTIONAL;
                $options = array();
                if ($isOptional) {
                    $options[0] = 'optional';
                }
                $options[1] = 'always';
                $options[2] = 'never';
                
                $data[$i]['apps'][$j] = array(
                    'id' => $app->id,
                    'name' => $app->name,
                    'options' => $options,
                    'selected' => $options[$selected],
                    'access' => $selected,
                    'is_optional' => $isOptional,
                );
            }
        }
        
        $this->output = json_encode(array(
            'data' => $data,
            'pagination' => array(
                'offset' => 0,
                'limit' => 20,
                'total' => count($this->actors),
            ),
        ));
        
        if (! empty($this->_padding)) {
            $this->output = $this->_padding.'('.$this->output.');';
        }
        
        return $this->output;
    }
}