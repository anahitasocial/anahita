<?php 

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Subscriptions
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Value added tax
 *
 * @category   Anahita
 * @package    Com_Subscriptions
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComSubscriptionsDomainEntityVat extends AnDomainEntityDefault
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
				'id',
				'country'	=> array('required'=>true, 'unique'=>true),
				'meta'		=> array('type'=>'json', 'column'=>'data', 'default'=>'json')
			)
		));
		
		parent::_initialize($config);
		
	}	
	
	/**
	 * Set the federal tax
	 * 
	 * @return void
	 */
	public function setFederalTax(array $taxes)
	{
		$meta  = clone $this->meta;
		
		$taxes  = new KConfig($taxes);
		$values = array(); 
		
		foreach($taxes as $tax) 
		{
			$value = $tax->value;
			$name  = $tax->name;
			
			if ( $value > 1) 
				$value = $value / 100;
				
			$values[strtolower($name)] = array('name'=>$name, 'value'=>min(max(0,$value), 1));	
		}
		
		$meta['federal_taxes'] = $values;
		
		$this->meta = $meta;
	}
		
	/**
	 * Returns an array of federal taxes
	 * 
	 * @return array
	 */
	public function getFederalTaxes()
	{
		$taxes = pick($this->meta->federal_taxes, array());
		foreach($taxes as &$tax)
			$tax = new KConfig($tax);
		return $taxes;
	}
}