<?php defined('JPATH_BASE') or die();
/**
 * @version		$Id$
 * @category	Anahita
 * @package		OpenSocial_Plugin
 * @copyright	Copyright (C) 2008 - 2010 rmdStudio Inc. and Peerglobe Technology Inc. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link     	http://www.anahitapolis.com
 */

/**
 * Profile Plugin
 *
 * @category	Anahita
 * @package		OpenSocial_Plugin
 */
class PlgProfileOpensocial extends PlgProfileAbstract
{

	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct($dispatcher, $params)
	{
		parent::__construct($dispatcher, $params);
		
		JFactory::getLanguage()->load('plg_profile_opensocial', JPATH_ADMINISTRATOR);
	}

	/**
	 * Called on the editing a profile field. Renders all the opensocail fields
	 *
	 * @param  KEvent $config
	 * @return void
	 */
	public function onSave(KEvent $config)
	{	    	    
		$actor 	 = $config->actor;
		
		if ( !is_person($actor) )
			return;
			
		$profile = KService::get('repos:opensocial.profile')->findOrAddNew(array('person'=>$actor));	
		
		$data = (array) KRequest::get('post.opensocial', 'string');
		
		$profile->setData($data);
		
		//formatting dateOfBirth
		if ( isset($data['dateOfBirth']) ) 
		{
			$dateOfBirth = array();
			$dateOfBirth['year']  = ($data['dateOfBirth']['year']) ? $data['dateOfBirth']['year'] : '0000';
			
			foreach($data['dateOfBirth'] as $index=>$value)
			{	
				if($index == 'month' || $index == 'day')
				{
					if($data['dateOfBirth'][$index])
					{
						$dateOfBirth[$index] = $data['dateOfBirth'][$index];
						if($dateOfBirth[$index] < 10)
							$dateOfBirth[$index] = '0'.$dateOfBirth[$index];
					}
					else 
						$dateOfBirth[$index] = '00';
				}
			}
			
			$profile->setDateOfBirth($dateOfBirth);
		}
	
		
		$profile->save();
	}

	/**
	 * Called on the editing a profile field. Renders all the opensocail fields
	 *
	 * @param  KEvent $config
	 * @return void
	 */
	public function onDisplay(KEvent $config)
	{
        
		if ( !is_person($config->actor) )
			return;
			
		$actor_profile = KService::get('repos:opensocial.profile')->findOrAddNew(array('person'=>$config->actor))->reset();
		$actor_profile->reset();		
		$osFields = $this->_getFields();
		
		$displayFields = array();
		
		foreach($osFields as $group => $fields)
			foreach($fields as $field => $type )
			{
				$value = $actor_profile->$field;
	
				$label = implode('-', KInflector::explode($field));
				
				if ( !empty($value) )
				{
					if($type == 'date')
					{	
						$date = new KDate(new KConfig(array('date'=>$value)));
						$value = explode('-', $value);
						
						$dateFormat = $this->_params->get('dateOfBirthFormat', '%e %B %Y');
						
						if( $value[0] == '0000')
							$dateFormat = str_replace(array('%Y', '%G', '%C'), '', $dateFormat);
							
						$displayFields[$group][$label] = $date->format($dateFormat);
						
						if($value[1] == '00' || $value[2] == '00')
						{
							unset($displayFields[$group][$label]);
						}
					}
					else
						$displayFields[$group][$label] = $value;
				}
			}
		
		$config->profile->append($displayFields);
	}

	/**
	 * Called on the editing a profile field. Renders all the opensocail fields
	 *
	 * @param  KEvent $config
	 * @return void
	 */
	public function onEdit(KEvent $config)
	{	    
		$actor 	 = $config->actor;
		
		if ( !is_person($actor) )
			return;
			
		$actor_profile = KService::get('repos:opensocial.profile')->findOrAddNew(array('person'=>$actor))->reset();
		$actor_profile->reset();
		$html 		= KService::get('com:base.template.helper.html');
		$sections	= array();
		$groups     = $this->_getFields();
		
		foreach($groups as $group_name => $fields)
		{		
			$sections[$group_name] = array();	
			foreach($fields as $variable => $type )
			{
				$data  = $actor_profile->$variable;			
				$name  = 'opensocial['.$variable.']';
				$label = implode('-', KInflector::explode($variable));
				if ( is_array($type) ) 
				{
					$values = array_combine($type, $type);
					array_unshift($values, 'DO-NOT-SHOW');
					foreach($values as $key => $value) 
						$values[$key] = JText::_($value);
						
					$value  = $html->select($name, array('options'=>$values, 'selected'=>$data));
				}
				elseif ( $type == 'date' ) 
				{
					$data = explode('-', $data);
					
					$selector   = KService::get('com:base.template.helper.selector');
					$date       = new KDate(new KConfig());
					$value      = $selector->month(array('name'=>$name.'[month]', 'selected'=>(int) @$data[1]))->class('input-medium').'&nbsp;'.
							      $selector->day(array('name'=>$name.'[day]', 'selected'=>(int) @$data[2]))->class('input-medium').'&nbsp;'.
							      $selector->year(array('name'=>$name.'[year]', 'start'=>1920, 'end'=>$date->year - 10, 'selected'=>(int) @$data[0]))->class('input-medium');
					
							      $value = $html->tag('span', $value);
				}				
				else $value = $html->$type($name, $data);
				
				if($value->name == 'textarea')
					$value->dataValidators('maxLength:500');
				
				$sections[$group_name][$label] = $value;
			}
		}
		
		$config->profile->append($sections);
	}
	
	/**
	 * Gets the fields
	 *
	 * @return array
	 */
	protected function _getFields()
	{	
		$osFields = array(
			'GROUP-ACTIVITIES-AND-INTERESTS' => array(		
				'activities'		=> 'textarea',
				'interests'			=> 'textarea',
				'jobInterests'		=> 'textarea',
				'books'				=> 'textarea',
				'quotes'			=> 'textarea',
				'heroes'			=> 'textarea',
				'music'				=> 'textarea',
				'tvShows'			=> 'textarea',
				'movies'			=> 'textarea',
				'sports'			=> 'textarea',
				'food'				=> 'textarea',
				'cars'				=> 'textarea',
				'humor'				=> 'textarea',
				'fashion'			=> 'textarea',
				'drinker'			=> array('YES','NO','HEAVILY','OCCASIONALLY','QUIT','QUITING','REGULARLY','SOCIALLY'),
				'smoker'			=> array('YES','NO','HEAVILY','OCCASIONALLY','QUIT','QUITING','REGULARLY','SOCIALLY'),
			),
			'GROUP-PERSONAL' => array(
				'livingArrangement'  => 'textarea',
				'dateOfBirth'        => 'date',
				'children'           => 'textarea',
				'scaredOf'           => 'textarea',
				'happiestWhen'       => 'textfield',
				'religion'           => 'textfield',
				'politicalViews'     => array('VERY-LIBERAL','LIBERAL','MODERATE','CONSERVATIVE','VERY-CONSERVATIVE','APATHETIC','LIBERTARIAN','OTHER')
			),
			'GROUP-RELATIONSHIP' => array(
				'relationshipStatus'	=> array('SINGLE','IN-A-RELATIONSHIP','COMMON-LAW','ENGAGED','MARRIED','OPEN'),
				'lookingFor'			=> 'textfield',
				'romance'				=> 'textfield',
				'ethnicity'				=> 'textfield',		
				'sexualOrientation'		=> array('STRAIGHT','GAY','LESBIAN','BISEXUAL','PANSEXUAL','OTHER'),
				'turnOns'		        => 'textarea',
				'turnOffs'		        => 'textarea',
				'build'			        => 'textfield',
				'eyeColor'	        	=> 'textfield',
				'height'	        	=> 'textfield',
				'weight'	        	=> 'textfield'
			)
		);
		
		foreach($osFields as $group => $fields) 
		{
			foreach($fields as $field => $type ) 
			{				
				if(!$this->_params->get($field))
					unset($osFields[$group][$field]);
			}
			
			//if group empty then unset the group
			if ( count($osFields[$group]) == 0 ) 
				unset($osFields[$group]);
		}
		
		return $osFields;
	}
}