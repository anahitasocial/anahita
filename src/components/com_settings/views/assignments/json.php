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

class ComSettingsViewAssignmentsJson extends ComBaseViewJson
{
    protected function _getList()
    {
        $components = $this->getService('repos:components.component')
                           ->getQuery()
                           ->enabled(true)
                           ->order('name')
                           ->fetchSet();

        $actors = array();

        foreach($components as $component) {
          $identifiers = $component->getEntityIdentifiers('ComActorsDomainEntityActor');
          foreach ($identifiers as $identifier) {
              $identifier->application = null;
              $actors[] = $identifier;
          }
        }

        $apps = array();
        
        foreach($components as $component) {
           if ($component->isAssignable()) {
              $apps[] = $component;
           }
        }
        
        $data = array();
        
        foreach($actors as $i => $actor) {
            $data[$i] = array(
                'identifier' => sprintf('com:%s.%s', $actor->package, $actor->name),
                'apps' => array(),
            );
            
            foreach($apps as $j => $app) {
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
        
        return array('data' => $data);
    }
}