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
 * Selector Helper provides various selection for list of months, years, days, countries, states and/or provinces
 * currency and usertypes
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseTemplateHelperSelector extends KTemplateHelperAbstract implements KServiceInstantiatable
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
	 * Constants
	 * 
	 */
	public static $COUNTRIES = array( 'GB' => 'United Kingdom', 'US' => 'United States', 'AF' => 'Afghanistan', 'AL' => 'Albania', 'DZ' => 'Algeria', 'AS' => 'American Samoa', 'AD' => 'Andorra', 'AO' => 'Angola', 'AI' => 'Anguilla', 'AQ' => 'Antarctica', 'AG' => 'Antigua And Barbuda', 'AR' => 'Argentina', 'AM' => 'Armenia', 'AW' => 'Aruba', 'AU' => 'Australia', 'AT' => 'Austria', 'AZ' => 'Azerbaijan', 'BS' => 'Bahamas', 'BH' => 'Bahrain', 'BD' => 'Bangladesh', 'BB' => 'Barbados', 'BY' => 'Belarus', 'BE' => 'Belgium', 'BZ' => 'Belize', 'BJ' => 'Benin', 'BM' => 'Bermuda', 'BT' => 'Bhutan', 'BO' => 'Bolivia', 'BA' => 'Bosnia And Herzegowina', 'BW' => 'Botswana', 'BV' => 'Bouvet Island', 'BR' => 'Brazil', 'IO' => 'British Indian Ocean Territory', 'BN' => 'Brunei Darussalam', 'BG' => 'Bulgaria', 'BF' => 'Burkina Faso', 'BI' => 'Burundi', 'KH' => 'Cambodia', 'CM' => 'Cameroon', 'CA' => 'Canada', 'CV' => 'Cape Verde', 'KY' => 'Cayman Islands', 'CF' => 'Central African Republic', 'TD' => 'Chad', 'CL' => 'Chile', 'CN' => 'China', 'CX' => 'Christmas Island', 'CC' => 'Cocos (Keeling) Islands', 'CO' => 'Colombia', 'KM' => 'Comoros', 'CG' => 'Congo', 'CD' => 'Congo, The Democratic Republic Of The', 'CK' => 'Cook Islands', 'CR' => 'Costa Rica', 'CI' => 'Cote D\'Ivoire', 'HR' => 'Croatia (Local Name: Hrvatska)', 'CU' => 'Cuba', 'CY' => 'Cyprus', 'CZ' => 'Czech Republic', 'DK' => 'Denmark', 'DJ' => 'Djibouti', 'DM' => 'Dominica', 'DO' => 'Dominican Republic', 'TP' => 'East Timor', 'EC' => 'Ecuador', 'EG' => 'Egypt', 'SV' => 'El Salvador', 'GQ' => 'Equatorial Guinea', 'ER' => 'Eritrea', 'EE' => 'Estonia', 'ET' => 'Ethiopia', 'FK' => 'Falkland Islands (Malvinas)', 'FO' => 'Faroe Islands', 'FJ' => 'Fiji', 'FI' => 'Finland', 'FR' => 'France', 'FX' => 'France, Metropolitan', 'GF' => 'French Guiana', 'PF' => 'French Polynesia', 'TF' => 'French Southern Territories', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Georgia', 'DE' => 'Germany', 'GH' => 'Ghana', 'GI' => 'Gibraltar', 'GR' => 'Greece', 'GL' => 'Greenland', 'GD' => 'Grenada', 'GP' => 'Guadeloupe', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GN' => 'Guinea', 'GW' => 'Guinea-Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HM' => 'Heard And Mc Donald Islands', 'VA' => 'Holy See (Vatican City State)', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Hungary', 'IS' => 'Iceland', 'IN' => 'India', 'ID' => 'Indonesia', 'IR' => 'Iran (Islamic Republic Of)', 'IQ' => 'Iraq', 'IE' => 'Ireland', 'IL' => 'Israel', 'IT' => 'Italy', 'JM' => 'Jamaica', 'JP' => 'Japan', 'JO' => 'Jordan', 'KZ' => 'Kazakhstan', 'KE' => 'Kenya', 'KI' => 'Kiribati', 'KP' => 'Korea, Democratic People\'s Republic Of', 'KR' => 'Korea, Republic Of', 'KW' => 'Kuwait', 'KG' => 'Kyrgyzstan', 'LA' => 'Lao People\'s Democratic Republic', 'LV' => 'Latvia', 'LB' => 'Lebanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libyan Arab Jamahiriya', 'LI' => 'Liechtenstein', 'LT' => 'Lithuania', 'LU' => 'Luxembourg', 'MO' => 'Macau', 'MK' => 'Macedonia, Former Yugoslav Republic Of', 'MG' => 'Madagascar', 'MW' => 'Malawi', 'MY' => 'Malaysia', 'MV' => 'Maldives', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshall Islands', 'MQ' => 'Martinique', 'MR' => 'Mauritania', 'MU' => 'Mauritius', 'YT' => 'Mayotte', 'MX' => 'Mexico', 'FM' => 'Micronesia, Federated States Of', 'MD' => 'Moldova, Republic Of', 'MC' => 'Monaco', 'MN' => 'Mongolia', 'MS' => 'Montserrat', 'MA' => 'Morocco', 'MZ' => 'Mozambique', 'MM' => 'Myanmar', 'NA' => 'Namibia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Netherlands', 'AN' => 'Netherlands Antilles', 'NC' => 'New Caledonia', 'NZ' => 'New Zealand', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'NU' => 'Niue', 'NF' => 'Norfolk Island', 'MP' => 'Northern Mariana Islands', 'NO' => 'Norway', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PA' => 'Panama', 'PG' => 'Papua New Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Philippines', 'PN' => 'Pitcairn', 'PL' => 'Poland', 'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Qatar', 'RE' => 'Reunion', 'RO' => 'Romania', 'RU' => 'Russian Federation', 'RW' => 'Rwanda', 'KN' => 'Saint Kitts And Nevis', 'LC' => 'Saint Lucia', 'VC' => 'Saint Vincent And The Grenadines', 'WS' => 'Samoa', 'SM' => 'San Marino', 'ST' => 'Sao Tome And Principe', 'SA' => 'Saudi Arabia', 'SN' => 'Senegal', 'SC' => 'Seychelles', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SK' => 'Slovakia (Slovak Republic)', 'SI' => 'Slovenia', 'SB' => 'Solomon Islands', 'SO' => 'Somalia', 'ZA' => 'South Africa', 'GS' => 'South Georgia, South Sandwich Islands', 'ES' => 'Spain', 'LK' => 'Sri Lanka', 'SH' => 'St. Helena', 'PM' => 'St. Pierre And Miquelon', 'SD' => 'Sudan', 'SR' => 'Suriname', 'SJ' => 'Svalbard And Jan Mayen Islands', 'SZ' => 'Swaziland', 'SE' => 'Sweden', 'CH' => 'Switzerland', 'SY' => 'Syrian Arab Republic', 'TW' => 'Taiwan', 'TJ' => 'Tajikistan', 'TZ' => 'Tanzania, United Republic Of', 'TH' => 'Thailand', 'TG' => 'Togo', 'TK' => 'Tokelau', 'TO' => 'Tonga', 'TT' => 'Trinidad And Tobago', 'TN' => 'Tunisia', 'TR' => 'Turkey', 'TM' => 'Turkmenistan', 'TC' => 'Turks And Caicos Islands', 'TV' => 'Tuvalu', 'UG' => 'Uganda', 'UA' => 'Ukraine', 'AE' => 'United Arab Emirates', 'UM' => 'United States Minor Outlying Islands', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistan', 'VU' => 'Vanuatu', 'VE' => 'Venezuela', 'VN' => 'Viet Nam', 'VG' => 'Virgin Islands (British)', 'VI' => 'Virgin Islands (U.S.)', 'WF' => 'Wallis And Futuna Islands', 'EH' => 'Western Sahara', 'YE' => 'Yemen', 'YU' => 'Yugoslavia', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe');
	public static $US_STATES = array('AL'=>'Alabama',   'AK'=>'Alaska',   'AZ'=>'Arizona',   'AR'=>'Arkansas',   'CA'=>'California',   'CO'=>'Colorado',   'CT'=>'Connecticut',   'DE'=>'Delaware',   'DC'=>'District Of Columbia',   'FL'=>'Florida',   'GA'=>'Georgia',   'HI'=>'Hawaii',   'ID'=>'Idaho',   'IL'=>'Illinois',   'IN'=>'Indiana',   'IA'=>'Iowa',   'KS'=>'Kansas',   'KY'=>'Kentucky',   'LA'=>'Louisiana',   'ME'=>'Maine',   'MD'=>'Maryland',   'MA'=>'Massachusetts',   'MI'=>'Michigan',   'MN'=>'Minnesota',   'MS'=>'Mississippi',   'MO'=>'Missouri',   'MT'=>'Montana', 'NE'=>'Nebraska', 'NV'=>'Nevada', 'NH'=>'New Hampshire', 'NJ'=>'New Jersey', 'NM'=>'New Mexico', 'NY'=>'New York', 'NC'=>'North Carolina', 'ND'=>'North Dakota', 'OH'=>'Ohio',   'OK'=>'Oklahoma',   'OR'=>'Oregon',   'PA'=>'Pennsylvania',   'RI'=>'Rhode Island',   'SC'=>'South Carolina',   'SD'=>'South Dakota', 'TN'=>'Tennessee',   'TX'=>'Texas',   'UT'=>'Utah',   'VT'=>'Vermont',   'VA'=>'Virginia',   'WA'=>'Washington',   'WV'=>'West Virginia',   'WI'=>'Wisconsin',   'WY'=>'Wyoming');
	public static $CANADA_PROVINCES = array('BC'=>'British Columbia',  'ON'=>'Ontario',  'NL'=>'Newfoundland and Labrador',  'NS'=>'Nova Scotia',  'PE'=>'Prince Edward Island',  'NB'=>'New Brunswick',  'QC'=>'Quebec',  'MB'=>'Manitoba',  'SK'=>'Saskatchewan',  'AB'=>'Alberta',  'NT'=>'Northwest Territories',  'NU'=>'Nunavut', 'YT'=>'Yukon Territory');
	public static $ISO_CURRENCY_SYMBOLS = array('AFA','AWG','AUD','ARS','AZN','BSD','BDT','BBD','BYR','BOB','BRL','GBP','BGN','KHR','CAD','KYD','CLP','CNY','COP','CRC','HRK','CPY','CZK','DKK','DOP','XCD','EGP','ERN','EEK','EUR','GEL','GHC','GIP','GTQ','HNL','HKD','HUF','ISK','INR','IDR','ILS','JMD','JPY','KZT','KES','KWD','LVL','LBP','LTL','MOP','MKD','MGA','MYR','MTL','BAM','MUR','MXN','MZM','NPR','ANG','TWD','NZD','NIO','NGN','KPW','NOK','OMR','PKR','PYG','PEN','PHP','QAR','RON','RUB','SAR','CSD','SCR','SGD','SKK','SIT','ZAR','KRW','LKR','SRD','SEK','CHF','TZS','THB','TTD','TRY','AED','USD','UGX','UAH','UYU','UZS','VEB','VND','AMK','ZWD');
	
	/**
	 * HTML Helper
	 * 
	 * @var LibBaseTemplateHelperHtml
	 */
	protected $_html;
	
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		
		$this->_html = $this->getService('com:base.template.helper.html');
	}
		
	/**
	 * Return a month selection from 1 to 12 or Jan to Feb
	 * 
	 * @param array $options
	 * @return LibBaseTemplateTag;
	 */
	public function month($options = array())
	{
		$options = new KConfig($options);
		
		$options->append(array(
			'selected'	=> null,
			'prompt'   => true
		));

		$selected  = $options->selected;
		unset($options->selected);	
		$prompt	   = $options->prompt;
		
		$months = array(
			1  => JText::_('LIB-AN-JANUARY'),
			2  => JText::_('LIB-AN-FEBRURARY'),
			3  => JText::_('LIB-AN-MARCH'),
			4  => JText::_('LIB-AN-APRIL'),
			5  => JText::_('LIB-AN-MAY'),
			6  => JText::_('LIB-AN-JUNE'),
			7  => JText::_('LIB-AN-JULY'),
			8  => JText::_('LIB-AN-AUGUST'),
			9  => JText::_('LIB-AN-SEPTEMBER'),
			10 => JText::_('LIB-AN-OCTOBER'),
			11 => JText::_('LIB-AN-NOVEMBER'),
			12 => JText::_('LIB-AN-DECEMBER')
		);
		
		unset($options->prompt);
		if ( $prompt ) {
			$array  = array(JText::_('LIB-AN-SELECTOR-SELECT-MONTH'));
			$months = AnHelperArray::merge($array, $months);
		}				
		
		
		return $this->_html->select($options->name, array('options'=>$months, 'selected'=>$selected), KConfig::unbox($options));
	}
	
	/**
	 * Return a month selection from 1 to 12 or Jan to Feb
	 * 
	 * @param array $options
	 * @return LibBaseTemplateTag;
	 */
	public function year($options = array())
	{
		$options = new KConfig($options);
		$date	 = new KDate(new KConfig());
		$year	 = $date->year;
		
		$options->append(array(
			'start' 	=> $year,
			'end'		=> $year + 10 ,
			'selected'	=> null,
			'prompt'   => true		
		));

		$selected  = $options->selected;
		unset($options->selected);	
		$prompt	   = $options->prompt;
		unset($options->prompt);
		$years = array_combine(range($options->start, $options->end), range($options->start, $options->end));
		
		if ( $prompt ) {
			$array  = array(JText::_('LIB-AN-SELECTOR-SELECT-YEAR'));
			$years = AnHelperArray::merge($array, $years);			
		}	
		
		
		return $this->_html->select($options->name, array('options'=>$years, 'selected'=>$selected), KConfig::unbox($options));
	}	
	
	/**
	 * Return a day selection from 1 to 31 
	 * 
	 * @param array $options
	 * @return LibBaseTemplateTag;
	 */
	public function day($options = array())
	{
		$options = new KConfig($options);
		
		$options->append(array(
			'selected' => null,
			'prompt'   => true,
		));
		
		$days 	   = array_combine(range(1, 31), range(1, 31));
		$selected  = $options->selected;		
		unset($options->selected);	
		$prompt	   = $options->prompt;
		unset($options->prompt);
		if ( $prompt ) {
			$array  = array(JText::_('LIB-AN-SELECTOR-SELECT-DAY'));
			$days = AnHelperArray::merge($array, $days);
		}			
		return $this->_html->select($options->name, array('options'=>$days, 'selected'=>$selected), KConfig::unbox($options));		
	}
		
	/**
	 * Return a selection of countries
	 * 
	 * @param array $options
	 * @return LibBaseTemplateTag
	 */
	public function country($options = array())
	{
		$options   = new KConfig($options);
		$options->append(array(
			'id'               => 'an-se-selection-countries-'.uniqid(),
			'selected'         => null,
			'name'	           => 'country'	,
			'use_country_code' => true,	
			'prompt'           => true
		));
		$selected  = $options->selected;
		$use_country_code = $options->use_country_code;
		unset($options->selected);		
		unset($options->use_country_code);		
		$countries = array();
		foreach(self::$COUNTRIES as $key => $name)
		{
			$countries[$use_country_code ? $key : $name] = JText::_($name);
		}
		
		return $this->_html->select($options->name, array('options'=>$countries, 'selected'=>$selected), KConfig::unbox($options));
	}
	
	/**
	 * Return a selection of states or provinces for canada
	 * 
	 * @param array $options
	 * @return LibBaseTemplateTag
	 */
	public function state($options = array())
	{
		$options   = new KConfig($options);
		
		$options->append(array(
			'country_selector' => null,
			'country'		   => 'US',
			'id'			   => uniqid(),
			'selected'		   => null,
			'use_state_code'   => true,
			'prompt'   => true		
		));
		
		
		$country = strtolower($options->country);

		
		if( $options->country_selector )
		{
			$doc = JFactory::getDocument();
			
			static $function_declration;
						
			if ( !$function_declration )
			{
				$function_declration = "function toggleStates(country, div){	
					div = document.id(div);
					div.set('html','');
					country = document.id(country);					
					var states 	  = div.retrieve('states');
					var provinces = div.retrieve('provinces');
					var custom 	  = div.retrieve('custom');
					if ( country.value == 'United States' || country.value == 'US')
						div.adopt(states);
					else if ( country.value == 'Canada' || country.value == 'CA' )
						div.adopt(provinces);
					else
						div.adopt(custom);
				}
				function storeElements(country, div)
				{
					div = document.id(div);
					var states 	  = div.getElement('[country=\"us\"]').clone();
					var provinces = div.getElement('[country=\"canada\"]').clone();
					var custom 	  = div.getElement('[country=\"custom\"]').clone();
					div.store('states', states).store('provinces',provinces).store('custom', custom);					
					toggleStates(country, div);	
				}
				";
				
				$doc->addScriptDeclaration($function_declration);
			}
			
			$div_id	= uniqid();
			$country_selector = $options->country_selector;
			$script = <<<EOF
			document.addEvent('domready', function(){
				storeElements('$country_selector','$div_id');
			});
			document.addEvent('change:relay(#$country_selector)', toggleStates.pass(['$country_selector', '$div_id']));
EOF;
			$doc->addScriptDeclaration($script);
			/*
			$doc
			->addScriptDeclaration("
				\$Event('#$div_id domready', storeElements.pass(['$country_selector', '$div_id']));
				\$Event('#$country_selector change',   toggleStates.pass(['$country_selector', '$div_id']));
			");*/
		}
		
		$use_state_code = $options->use_state_code;
		
		unset($options->use_state_code);
		unset($options->country);
		unset($options->country_selector);
				
		$states    = array();
		$provinces = array();
		
		foreach(self::$US_STATES as $key => $name)
		{
			$states[$use_state_code ? $key : $name] = JText::_($name);
		}
		
		foreach(self::$CANADA_PROVINCES as $key => $name)
			$provinces[$use_state_code ? $key : $name] = JText::_($name);

		$selected  = $options->selected;
		unset($options->selected);
		$options   = KConfig::unbox($options);
		
		$states    = $this->_html->select($options['name'], array('options'=>$states, 'selected'=>$selected), 	 array_merge($options, array('country'=>'us')));
		$provinces = $this->_html->select($options['name'], array('options'=>$provinces, 'selected'=>$selected), array_merge($options, array('country'=>'canada')));
		$custom	   = $this->_html->textfield($options['name'], $selected, array_merge($options, array('country'=>'custom')));			
		if ( empty($country_selector) )
		{
			return $country == 'us' ? $states : $provinces;
		}
		
		return "<div id=\"$div_id\">$states.$provinces.$custom</div>";		 
	}

	/**
	 * Return a selection of standard currency codes
	 * 
	 * @param array $options
	 * @return LibBaseTemplateTag
	 */
	public function currency($options = array())
	{
		$options   = new KConfig($options);
		
		$options->append(array(
			'name' => 'currency',
			'prompt'   => true		
		));
		
		$selected = $options->selected;
		unset($options->selected);		
		$currencies = array_combine(self::$ISO_CURRENCY_SYMBOLS, self::$ISO_CURRENCY_SYMBOLS);
		
		return $this->_html->select($options->name, array('options'=>$currencies, 'selected'=>$selected), KConfig::unbox($options));
	}	
	
	/**
	 * Returns a user type selector
	 * 
	 * @param array $options
	 * @return string
	 */
	public function usertypes($options = array())
	{
		$options   = new KConfig($options);
		
		$options->append(array('root_name'=>'Registered', 'inclusive'=>true, 'multiple_selection'=>true));

		
		$acl		=& JFactory::getACL();
		
		$gtree = $acl->get_group_children_tree(null, $options->root_name, $options->inclusive );

			
		$attr 		= 'class="user-types" size="'.count($gtree).'"';
		
		if ( $options->multiple_selection ) {
			$attr .= ' multiple ';
		}
			
		return JHTML::_('select.genericlist',  $gtree, 'gid', $attr, 'value', 'text', $options->selected);
	}	
	
	
}