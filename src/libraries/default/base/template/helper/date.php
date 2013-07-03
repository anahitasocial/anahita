<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Date Helper
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseTemplateHelperDate extends KTemplateHelperAbstract implements KServiceInstantiatable
{
    /**
     * Force creation of a singleton
     *
     * @param KConfigInterface 	$config    An optional KConfig object with configuration options
     * @param KServiceInterface	$container A KServiceInterface object
     *
     * @return KServiceInstantiatable
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        if (!$container->has($config->service_identifier))
        {
            $classname = $config->service_identifier->classname;
            $instance  = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }
    
        return $container->get($config->service_identifier);
    }
        
	/**
	 * current time
	 * 
	 * @var KDate
	 */
	protected $_current_time;
	
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		
		//load the com_Anahita_SocialCore language for the date related string		
		$this->_current_time = AnDomainAttributeDate::getInstance();
	}
	
	/**
	 * Date picker. Return a selcetor with all the date components 
	 *
	 * @param string   $name 
	 * @param KDate	   $date
	 * @param array    $options
	 */
	public function picker($name, $options = array())
	{
		$options = new KConfig($options);
		
		$options->append(array(
			'date' => new KDate() 
		));
		
		$date = $options->date;
		
		$html = $this->getService('com:base.template.helper.html');
		
		if ( is_string($date) ) {
			$date = new KDate(new KConfig(array('date'=>$date)));	
		}
			
		$month   = $date->month;
		$year	 = $date->year;
		$day	 = $date->day;
				
		$months  = array(
			0 => 'Select Month',
			1 => JText::_('JANUARY') ,
			2 => JText::_('FEBRUARY') ,
			3 => JText::_('MARCH')  ,
			4 => JText::_('APRIL')  ,
			5 => JText::_('MAY')    ,
			6 => JText::_('JUNE')   ,
			7 => JText::_('JULY') 	,
			8 => JText::_('AUGUST') ,
			9 => JText::_('SEPTEMBER') ,
			10 => JText::_('OCTOBER') ,
			11 => JText::_('NOVEMBER') ,
			12 => JText::_('DECEMBER') ,												
		);
		
		$days  = array(0=>'Select Day');
		$years = array(0=>'Select Year');
		
		foreach(range(1,31) as $i=>$num) $days[$i+1] = $num;
		
		$current  = new KDate();

		foreach(range(0,100) as $i) {
			$years[$current->year + $i] = $current->year + $i;
		}

		$year 	 	= $html->select($name.'[year]',  array('options'=>$years,  'selected'=>$year))->class('input-medium');
		$month   	= $html->select($name.'[month]', array('options'=>$months, 'selected'=>$month))->class('input-medium');
		$day	 	= $html->select($name.'[day]',   array('options'=>$days,   'selected'=>$day))->class('input-small');
		
		
		return $year.' '.$month.' '.$day;		
	}
	
	/**
	 * Return a human friendly format of the date
	 * 
	 * @param KDate $date 
	 * @param array $config Optional array to pass format 
	 * @return string
	 */
	public function humanize($date, $config=array())
	{	
		$config   = new KConfig($config);
		
		$config->append(array(
			'format'   => '%B %d %Y' , 
			'relative' => false		 ,
			'offset'   => null
		));	
		
		$format = $config->format;
		
		$diff = $this->_current_time->getDate(DATE_FORMAT_UNIXTIME) - $date->getDate(DATE_FORMAT_UNIXTIME);
		
		if ( $config->relative )
		{
			$timeLeft = ($diff < 0 ) ? '-FUTURE' : ''; 
			$diff     = abs($diff);
			
			if ($diff < 1 )
				return sprintf(JText::_('LIB-AN-DATE-MOMENT'), $diff);
				
			if ($diff < 60)
				return ( $diff > 1 ) ? sprintf(JText::_('LIB-AN-DATE-SECONDS'.$timeLeft), $diff) : sprintf(JText::_('LIB-AN-DATE-SECOND'.$timeLeft), $diff);
			
			$diff = round($diff/60);
			
			if ($diff<60)
				return ( $diff > 1 ) ? sprintf(JText::_('LIB-AN-DATE-MINUTES'.$timeLeft), $diff) : sprintf(JText::_('LIB-AN-DATE-MINUTE'.$timeLeft), $diff);
				
			$diff = round($diff/60);
			if ($diff<24)				
				return ( $diff > 1 ) ? sprintf(JText::_('LIB-AN-DATE-HOURS'.$timeLeft), $diff) : sprintf(JText::_('LIB-AN-DATE-HOUR'.$timeLeft), $diff);
				
			$diff = round($diff/24);
			if ($diff<7)
				return ( $diff > 1 ) ? sprintf(JText::_('LIB-AN-DATE-DAYS'.$timeLeft), $diff) : sprintf(JText::_('LIB-AN-DATE-DAY'.$timeLeft), $diff);
				
			$diff = round($diff/7);
			
			if ($diff<4)
				return ( $diff > 1 ) ? sprintf(JText::_('LIB-AN-DATE-WEEKS'.$timeLeft), $diff) : sprintf(JText::_('LIB-AN-DATE-WEEK'.$timeLeft), $diff);

		} else if ( $config->offset )
		{
			$date->addHours($config->offset);
		}
		
		return $date->getDate($format);
	}			
}
?>