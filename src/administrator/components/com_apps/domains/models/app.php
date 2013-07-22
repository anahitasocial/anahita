<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_Apps
 * @subpackage Domain
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Model to manage app and actortype synchronization
 * 
 * @category   Anahita
 * @package    Com_Apps
 * @subpackage Domain
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComAppsDomainModelApp extends KObject 
{
    //@TODO need make the  ComAppsDomainModelApp an instantiatable object
        
    /**
     * Array of actor identifiers
     * 
     * array 
     */
    protected static $_identifiers;
    
	/**
	 * Discovers an array of actor identifiers for an app 
	 *
	 * @param ComAppsDomainEntityApp $app An App entity
	 * 
	 * @return array
	 */
	static public function getActorIdentifiers($app)
	{
        $identifiers = self::findActorIdentifiers();
        $tmp         = array();
        foreach($identifiers as $identifier) {
            if ( $identifier->package != $app->getDelegate()->getIdentifier()->package )
                $tmp[] = clone $identifier;
        }
        return $tmp;
	}
	
    /**
     * Return an array of actor identifiers
     * 
     * @return array
     */
    static public function findActorIdentifiers()
    {
        if ( !isset(self::$_identifiers) )
        {
            self::$_identifiers = array();
            $components  = KService::get('repos:components.component', array('resources'=>'components'))->fetchSet()->option;           
            foreach($components as $component)
            {
                $path = JPATH_SITE.'/components/'.$component.'/domains/entities';
                
                if ( !file_exists($path) )
                    continue;
                
                //get all the files
                $files = (array)JFolder::files($path);
                //convert com_<Component> to ['com','<Name>']           
                $parts      = explode('_', $component);         
                $identifier = new KServiceIdentifier('com:'. substr($component, strpos($component, '_') + 1));
                $identifier->path = array('domain', 'entity');
                
                foreach($files as $file)
                {
                    $identifier->name = substr($file, 0, strpos($file, '.'));               
                    try
                    {
                        if ( is($identifier->classname, 'ComActorsDomainEntityActor') ) 
                        {
                            self::$_identifiers[] = clone $identifier;
                        }
                    } 
                    catch(Exception $e) {  }
                }
            }            
        }
        return self::$_identifiers;        
    }
        
	/**
	 * Sync the apps
	 * 
	 * @return void
	 */
	static public function syncApps()
	{
		$components = KService::get('repos:components.component', array('resources'=>'components'))->fetchSet();
		$apps 		= KService::get('repos:apps.app')->fetchSet();
		$options	= array_unique($components->option);
		foreach($apps as $app) 
		{
			if ( !in_array($app->component, $options) )
				$app->destroy();
		}
		foreach($options as $option) 
		{
			$path  = JPATH_SITE.DS.'components'.DS.$option.DS.'delegate.php';
			$app   = $apps->find(array('component'=>$option));
			$exist = file_exists($path);
			if ( !$exist && $app ) {
				$app->destroy();
			} elseif ( $exist ) {
                if ( !$app ) {
				    $app = KService::get('repos:apps.app')->getEntity(array('data'=>array('component'=>$option)));
                }

                if ( $app->getAssignmentOption() == ComAppsDomainDelegateDefault::ASSIGNMENT_OPTION_ALWAYS ||
                     $app->getAssignmentOption() == ComAppsDomainDelegateDefault::ASSIGNMENT_OPTION_NEVER
                 ) 
                {
                    $app->installs->delete();
                    $app->actortypes->delete();
                    $app->always = $app->getAssignmentOption() == ComAppsDomainDelegateDefault::ASSIGNMENT_OPTION_ALWAYS;
                } else {
                    $app->always = null;
                }
                
                $assignments = $app->getDefaultAssignments();
                //if app is not persisted (new) set the default assignments
                if ( !$app->persisted() && count($assignments) )
                {
                    $values = array();
                    foreach(self::findActorIdentifiers() as $identifier)
                    {     
                        $strIdentifier = (string) $identifier;
                        $assignment    = ComAppsDomainDelegateDefault::ASSIGNMENT_OPTIONAL;
                        if ( isset($assignments[$strIdentifier]) ) {
                            $assignment =  $assignments[$strIdentifier];  
                        } elseif ( isset($assignments[$identifier->name]) ) { 
                            $assignment =  $assignments[$identifier->name];
                        }
                        $values[$strIdentifier] = $assignment; 
                    }
                    $app->assignTo($values);
                }
                
                $app->save();
			}
		}
	}
}