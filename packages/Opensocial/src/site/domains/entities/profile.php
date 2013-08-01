<?php
/**
* @version		$Id$
 * @category	Anahita
 * @package 	Anahita_Sengine
 * @subpackage 	Person
 * @copyright	Copyright (C) 2008 - 2010 rmdStudio Inc. and Peerglobe Technology Inc. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link     	http://www.anahitapolis.com
 * @author		Arash Sanieyan
 */

/**
 * Person profile fields
 *
 */
class ComOpensocialDomainEntityProfile extends AnDomainEntityDefault 
{
	/**
	 * Initializes the default configuration for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param KConfig $config An optional KConfig object with configuration options.
	 *
	 * @return void
	 */
	protected function _initialize(KConfig $config)
	{	
		$config->append(array(			
			'attributes' => array(
				'id' => array('key'=>true),
				'sexualOrientation',
				'relationshipStatus',
				'honorificPrefix',
				'honorificSuffix',
				'dateOfBirth',
				'build',
				'eyeColor',
				'hairColor',
				'height',
				'weight',
				'activities',
				'books',
				'cars',
				'children',
				'drinker',
				'ethnicity',
				'fashion',
				'food',
				'happiestWhen',
				'heroes',
				'humor',
				'interests',
				'jobInterests',
				'livingArrangement',
				'lookingFor',
				'movies',
				'music',
				'pets',
				'politicalViews',
				'quotes',
				'religion',
				'romance',
				'scaredOf',
				'smoker',
				'sports',
				'turnOffs',
				'turnOns',
				'tvShows'
			),
			'relationships' => array(
				'person' => array('parent'=>'com:people.domain.entity.person', 'child_column'=>'socialengine_actor_id','required'=>true)
			)
		));
				
		parent::_initialize($config);
	}
	
	/**
	 * Set date of birth
	 * 
	 * @param array|string $date The date. Can be a string or an array
	 * 
	 * @return void
	 */
	public function setDateOfBirth($date=array())
	{
		if ( is_array($date) ) 
		{
			$date = new KConfig($date);
			
			$date->append(array(
				'day' => '00',
				'month' => '00',
				'year' => '0000'
			));
			
			$date = KConfig::unbox($date);
			$date = implode('-', $date);
		}
		$this->set('dateOfBirth', $date);
	}
	
//end class	
}