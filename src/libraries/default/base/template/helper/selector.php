<?php
/**
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibBaseTemplateHelperSelector extends LibBaseTemplateHelperAbstract implements KServiceInstantiatable
{
    /**
     * Force creation of a singleton.
     *
     * @param KConfigInterface  $config    An optional KConfig object with configuration options
     * @param KServiceInterface $container A KServiceInterface object
     *
     * @return KServiceInstantiatable
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        if (!$container->has($config->service_identifier)) {
            $classname = $config->service_identifier->classname;
            $instance = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }

        return $container->get($config->service_identifier);
    }

    /**
     * Constants.
     */
    public static $COUNTRIES = array(
        "AF" => "Afghanistan (‫افغانستان‬‎)",
        "AX" => "Åland Islands (Åland)",
        "AL" => "Albania (Shqipëri)",
        "DZ" => "Algeria (‫الجزائر‬‎)",
        "AS" => "American Samoa",
        "AD" => "Andorra",
        "AO" => "Angola",
        "AI" => "Anguilla",
        "AQ" => "Antarctica",
        "AG" => "Antigua and Barbuda",
        "AR" => "Argentina",
        "AM" => "Armenia (Հայաստան)",
        "AW" => "Aruba",
        "AC" => "Ascension Island",
        "AU" => "Australia",
        "AT" => "Austria (Österreich)",
        "AZ" => "Azerbaijan (Azərbaycan)",
        "BS" => "Bahamas",
        "BH" => "Bahrain (‫البحرين‬‎)",
        "BD" => "Bangladesh (বাংলাদেশ)",
        "BB" => "Barbados",
        "BY" => "Belarus (Беларусь)",
        "BE" => "Belgium (België)",
        "BZ" => "Belize",
        "BJ" => "Benin (Bénin)",
        "BM" => "Bermuda",
        "BT" => "Bhutan (འབྲུག)",
        "BO" => "Bolivia",
        "BA" => "Bosnia and Herzegovina (Босна и Херцеговина)",
        "BW" => "Botswana",
        "BV" => "Bouvet Island",
        "BR" => "Brazil (Brasil)",
        "IO" => "British Indian Ocean Territory",
        "VG" => "British Virgin Islands",
        "BN" => "Brunei",
        "BG" => "Bulgaria (България)",
        "BF" => "Burkina Faso",
        "BI" => "Burundi (Uburundi)",
        "KH" => "Cambodia (កម្ពុជា)",
        "CM" => "Cameroon (Cameroun)",
        "CA" => "Canada",
        "IC" => "Canary Islands (islas Canarias)",
        "CV" => "Cape Verde (Kabu Verdi)",
        "BQ" => "Caribbean Netherlands",
        "KY" => "Cayman Islands",
        "CF" => "Central African Republic (République centrafricaine)",
        "EA" => "Ceuta and Melilla (Ceuta y Melilla)",
        "TD" => "Chad (Tchad)",
        "CL" => "Chile",
        "CN" => "China (中国)",
        "CX" => "Christmas Island",
        "CP" => "Clipperton Island",
        "CC" => "Cocos (Keeling) Islands (Kepulauan Cocos (Keeling))",
        "CO" => "Colombia",
        "KM" => "Comoros (‫جزر القمر‬‎)",
        "CD" => "Congo (DRC) (Jamhuri ya Kidemokrasia ya Kongo)",
        "CG" => "Congo (Republic) (Congo-Brazzaville)",
        "CK" => "Cook Islands",
        "CR" => "Costa Rica",
        "CI" => "Côte d’Ivoire",
        "HR" => "Croatia (Hrvatska)",
        "CU" => "Cuba",
        "CW" => "Curaçao",
        "CY" => "Cyprus (Κύπρος)",
        "CZ" => "Czech Republic (Česká republika)",
        "DK" => "Denmark (Danmark)",
        "DG" => "Diego Garcia",
        "DJ" => "Djibouti",
        "DM" => "Dominica",
        "DO" => "Dominican Republic (República Dominicana)",
        "EC" => "Ecuador",
        "EG" => "Egypt (‫مصر‬‎)",
        "SV" => "El Salvador",
        "GQ" => "Equatorial Guinea (Guinea Ecuatorial)",
        "ER" => "Eritrea",
        "EE" => "Estonia (Eesti)",
        "ET" => "Ethiopia",
        "FK" => "Falkland Islands (Islas Malvinas)",
        "FO" => "Faroe Islands (Føroyar)",
        "FJ" => "Fiji",
        "FI" => "Finland (Suomi)",
        "FR" => "France",
        "GF" => "French Guiana (Guyane française)",
        "PF" => "French Polynesia (Polynésie française)",
        "TF" => "French Southern Territories (Terres australes françaises)",
        "GA" => "Gabon",
        "GM" => "Gambia",
        "GE" => "Georgia (საქართველო)",
        "DE" => "Germany (Deutschland)",
        "GH" => "Ghana (Gaana)",
        "GI" => "Gibraltar",
        "GR" => "Greece (Ελλάδα)",
        "GL" => "Greenland (Kalaallit Nunaat)",
        "GD" => "Grenada",
        "GP" => "Guadeloupe",
        "GU" => "Guam",
        "GT" => "Guatemala",
        "GG" => "Guernsey",
        "GN" => "Guinea (Guinée)",
        "GW" => "Guinea-Bissau (Guiné Bissau)",
        "GY" => "Guyana",
        "HT" => "Haiti",
        "HM" => "Heard & McDonald Islands",
        "HN" => "Honduras",
        "HK" => "Hong Kong (香港)",
        "HU" => "Hungary (Magyarország)",
        "IS" => "Iceland (Ísland)",
        "IN" => "India (भारत)",
        "ID" => "Indonesia",
        "IR" => "Iran (‫ایران‬‎)",
        "IQ" => "Iraq (‫العراق‬‎)",
        "IE" => "Ireland",
        "IM" => "Isle of Man",
        "IL" => "Israel (‫ישראל‬‎)",
        "IT" => "Italy (Italia)",
        "JM" => "Jamaica",
        "JP" => "Japan (日本)",
        "JE" => "Jersey",
        "JO" => "Jordan (‫الأردن‬‎)",
        "KZ" => "Kazakhstan (Казахстан)",
        "KE" => "Kenya",
        "KI" => "Kiribati",
        "XK" => "Kosovo (Kosovë)",
        "KW" => "Kuwait (‫الكويت‬‎)",
        "KG" => "Kyrgyzstan (Кыргызстан)",
        "LA" => "Laos (ລາວ)",
        "LV" => "Latvia (Latvija)",
        "LB" => "Lebanon (‫لبنان‬‎)",
        "LS" => "Lesotho",
        "LR" => "Liberia",
        "LY" => "Libya (‫ليبيا‬‎)",
        "LI" => "Liechtenstein",
        "LT" => "Lithuania (Lietuva)",
        "LU" => "Luxembourg",
        "MO" => "Macau (澳門)",
        "MK" => "Macedonia (FYROM) (Македонија)",
        "MG" => "Madagascar (Madagasikara)",
        "MW" => "Malawi",
        "MY" => "Malaysia",
        "MV" => "Maldives",
        "ML" => "Mali",
        "MT" => "Malta",
        "MH" => "Marshall Islands",
        "MQ" => "Martinique",
        "MR" => "Mauritania (‫موريتانيا‬‎)",
        "MU" => "Mauritius (Moris)",
        "YT" => "Mayotte",
        "MX" => "Mexico (México)",
        "FM" => "Micronesia",
        "MD" => "Moldova (Republica Moldova)",
        "MC" => "Monaco",
        "MN" => "Mongolia (Монгол)",
        "ME" => "Montenegro (Crna Gora)",
        "MS" => "Montserrat",
        "MA" => "Morocco (‫المغرب‬‎)",
        "MZ" => "Mozambique (Moçambique)",
        "MM" => "Myanmar (Burma) (မြန်မာ)",
        "NA" => "Namibia (Namibië)",
        "NR" => "Nauru",
        "NP" => "Nepal (नेपाल)",
        "NL" => "Netherlands (Nederland)",
        "NC" => "New Caledonia (Nouvelle-Calédonie)",
        "NZ" => "New Zealand",
        "NI" => "Nicaragua",
        "NE" => "Niger (Nijar)",
        "NG" => "Nigeria",
        "NU" => "Niue",
        "NF" => "Norfolk Island",
        "MP" => "Northern Mariana Islands",
        "KP" => "North Korea (조선 민주주의 인민 공화국)",
        "NO" => "Norway (Norge)",
        "OM" => "Oman (‫عُمان‬‎)",
        "PK" => "Pakistan (‫پاکستان‬‎)",
        "PW" => "Palau",
        "PS" => "Palestine (‫فلسطين‬‎)",
        "PA" => "Panama (Panamá)",
        "PG" => "Papua New Guinea",
        "PY" => "Paraguay",
        "PE" => "Peru (Perú)",
        "PH" => "Philippines",
        "PN" => "Pitcairn Islands",
        "PL" => "Poland (Polska)",
        "PT" => "Portugal",
        "PR" => "Puerto Rico",
        "QA" => "Qatar (‫قطر‬‎)",
        "RE" => "Réunion (La Réunion)",
        "RO" => "Romania (România)",
        "RU" => "Russia (Россия)",
        "RW" => "Rwanda",
        "BL" => "Saint Barthélemy (Saint-Barthélemy)",
        "SH" => "Saint Helena",
        "KN" => "Saint Kitts and Nevis",
        "LC" => "Saint Lucia",
        "MF" => "Saint Martin (Saint-Martin (partie française))",
        "PM" => "Saint Pierre and Miquelon (Saint-Pierre-et-Miquelon)",
        "WS" => "Samoa",
        "SM" => "San Marino",
        "ST" => "São Tomé and Príncipe (São Tomé e Príncipe)",
        "SA" => "Saudi Arabia (‫المملكة العربية السعودية‬‎)",
        "SN" => "Senegal (Sénégal)",
        "RS" => "Serbia (Србија)",
        "SC" => "Seychelles",
        "SL" => "Sierra Leone",
        "SG" => "Singapore",
        "SX" => "Sint Maarten",
        "SK" => "Slovakia (Slovensko)",
        "SI" => "Slovenia (Slovenija)",
        "SB" => "Solomon Islands",
        "SO" => "Somalia (Soomaaliya)",
        "ZA" => "South Africa",
        "GS" => "South Georgia & South Sandwich Islands",
        "KR" => "South Korea (대한민국)",
        "SS" => "South Sudan (‫جنوب السودان‬‎)",
        "ES" => "Spain (España)",
        "LK" => "Sri Lanka (ශ්‍රී ලංකාව)",
        "VC" => "St. Vincent & Grenadines",
        "SD" => "Sudan (‫السودان‬‎)",
        "SR" => "Suriname",
        "SJ" => "Svalbard and Jan Mayen (Svalbard og Jan Mayen)",
        "SZ" => "Swaziland",
        "SE" => "Sweden (Sverige)",
        "CH" => "Switzerland (Schweiz)",
        "SY" => "Syria (‫سوريا‬‎)",
        "TW" => "Taiwan (台灣)",
        "TJ" => "Tajikistan",
        "TZ" => "Tanzania",
        "TH" => "Thailand (ไทย)",
        "TL" => "Timor-Leste",
        "TG" => "Togo",
        "TK" => "Tokelau",
        "TO" => "Tonga",
        "TT" => "Trinidad and Tobago",
        "TA" => "Tristan da Cunha",
        "TN" => "Tunisia (‫تونس‬‎)",
        "TR" => "Turkey (Türkiye)",
        "TM" => "Turkmenistan",
        "TC" => "Turks and Caicos Islands",
        "TV" => "Tuvalu",
        "UM" => "U.S. Outlying Islands",
        "VI" => "U.S. Virgin Islands",
        "UG" => "Uganda",
        "UA" => "Ukraine (Україна)",
        "AE" => "United Arab Emirates (‫الإمارات العربية المتحدة‬‎)",
        "GB" => "United Kingdom",
        "US" => "United States",
        "UY" => "Uruguay",
        "UZ" => "Uzbekistan (Oʻzbekiston)",
        "VU" => "Vanuatu",
        "VA" => "Vatican City (Città del Vaticano)",
        "VE" => "Venezuela",
        "VN" => "Vietnam (Việt Nam)",
        "WF" => "Wallis and Futuna",
        "EH" => "Western Sahara (‫الصحراء الغربية‬‎)",
        "YE" => "Yemen (‫اليمن‬‎)",
        "ZM" => "Zambia",
        "ZW" => "Zimbabwe"
    );

    public static $US_STATES = array(
        'AL' => 'Alabama',
        'AK' => 'Alaska',
        'AZ' => 'Arizona',
        'AR' => 'Arkansas',
        'CA' => 'California',
        'CO' => 'Colorado',
        'CT' => 'Connecticut',
        'DE' => 'Delaware',
        'DC' => 'District Of Columbia',
        'FL' => 'Florida',
        'GA' => 'Georgia',
        'HI' => 'Hawaii',
        'ID' => 'Idaho',
        'IL' => 'Illinois',
        'IN' => 'Indiana',
        'IA' => 'Iowa',
        'KS' => 'Kansas',
        'KY' => 'Kentucky',
        'LA' => 'Louisiana',
        'ME' => 'Maine',
        'MD' => 'Maryland',
        'MA' => 'Massachusetts',
        'MI' => 'Michigan',
        'MN' => 'Minnesota',
        'MS' => 'Mississippi',
        'MO' => 'Missouri',
        'MT' => 'Montana',
        'NE' => 'Nebraska',
        'NV' => 'Nevada',
        'NH' => 'New Hampshire',
        'NJ' => 'New Jersey',
        'NM' => 'New Mexico',
        'NY' => 'New York',
        'NC' => 'North Carolina',
        'ND' => 'North Dakota',
        'OH' => 'Ohio',
        'OK' => 'Oklahoma',
        'OR' => 'Oregon',
        'PA' => 'Pennsylvania',
        'RI' => 'Rhode Island',
        'SC' => 'South Carolina',
        'SD' => 'South Dakota',
        'TN' => 'Tennessee',
        'TX' => 'Texas',
        'UT' => 'Utah',
        'VT' => 'Vermont',
        'VA' => 'Virginia',
        'WA' => 'Washington',
        'WV' => 'West Virginia',
        'WI' => 'Wisconsin',
        'WY' => 'Wyoming'
    );

    public static $CANADA_PROVINCES = array(
        'BC' => 'British Columbia',
        'ON' => 'Ontario',
        'NL' => 'Newfoundland and Labrador',
        'NS' => 'Nova Scotia',
        'PE' => 'Prince Edward Island',
        'NB' => 'New Brunswick',
        'QC' => 'Quebec',
        'MB' => 'Manitoba',
        'SK' => 'Saskatchewan',
        'AB' => 'Alberta',
        'NT' => 'Northwest Territories',
        'NU' => 'Nunavut',
        'YT' => 'Yukon Territory'
    );

    public static $ISO_CURRENCY_SYMBOLS = array('AFA','AWG','AUD','ARS','AZN','BSD','BDT','BBD','BYR','BOB','BRL','GBP','BGN','KHR','CAD','KYD','CLP','CNY','COP','CRC','HRK','CPY','CZK','DKK','DOP','XCD','EGP','ERN','EEK','EUR','GEL','GHC','GIP','GTQ','HNL','HKD','HUF','ISK','INR','IDR','ILS','JMD','JPY','KZT','KES','KWD','LVL','LBP','LTL','MOP','MKD','MGA','MYR','MTL','BAM','MUR','MXN','MZM','NPR','ANG','TWD','NZD','NIO','NGN','KPW','NOK','OMR','PKR','PYG','PEN','PHP','QAR','RON','RUB','SAR','CSD','SCR','SGD','SKK','SIT','ZAR','KRW','LKR','SRD','SEK','CHF','TZS','THB','TTD','TRY','AED','USD','UGX','UAH','UYU','UZS','VEB','VND','AMK','ZWD');

    /**
     * HTML Helper.
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
     * Return a month selection from 1 to 12 or Jan to Feb.
     *
     * @param array $options
     *
     * @return LibBaseTemplateTag;
     */
    public function month($options = array())
    {
        $options = new KConfig($options);

        $options->append(array(
            'selected' => null,
            'prompt' => true,
        ));

        $selected = $options->selected;
        unset($options->selected);
        $prompt = $options->prompt;

        $months = array(
            1 => AnTranslator::_('LIB-AN-JANUARY'),
            2 => AnTranslator::_('LIB-AN-FEBRURARY'),
            3 => AnTranslator::_('LIB-AN-MARCH'),
            4 => AnTranslator::_('LIB-AN-APRIL'),
            5 => AnTranslator::_('LIB-AN-MAY'),
            6 => AnTranslator::_('LIB-AN-JUNE'),
            7 => AnTranslator::_('LIB-AN-JULY'),
            8 => AnTranslator::_('LIB-AN-AUGUST'),
            9 => AnTranslator::_('LIB-AN-SEPTEMBER'),
            10 => AnTranslator::_('LIB-AN-OCTOBER'),
            11 => AnTranslator::_('LIB-AN-NOVEMBER'),
            12 => AnTranslator::_('LIB-AN-DECEMBER'),
        );

        unset($options->prompt);

        if ($prompt) {
            $array = array(AnTranslator::_('LIB-AN-SELECTOR-SELECT-MONTH'), null);
            $months = AnHelperArray::merge($array, $months);
        }

        return $this->_html->select($options->name, array('options' => $months, 'selected' => $selected), KConfig::unbox($options));
    }

    /**
     * Return a month selection from 1 to 12 or Jan to Feb.
     *
     * @param array $options
     *
     * @return LibBaseTemplateTag;
     */
    public function year($options = array())
    {
        $options = new KConfig($options);
        $date = new AnDate(new KConfig());
        $year = $date->year;

        $options->append(array(
            'start' => $year,
            'end' => $year + 10,
            'selected' => null,
            'prompt' => true,
        ));

        $selected = $options->selected;
        unset($options->selected);
        $prompt = $options->prompt;
        unset($options->prompt);
        $years = array_combine(range($options->start, $options->end), range($options->start, $options->end));

        if ($prompt) {
            $array = array(AnTranslator::_('LIB-AN-SELECTOR-SELECT-YEAR'));
            $years = AnHelperArray::merge($array, $years);
        }

        return $this->_html->select($options->name, array('options' => $years, 'selected' => $selected), KConfig::unbox($options));
    }

    /**
     * Return a day selection from 1 to 31.
     *
     * @param array $options
     *
     * @return LibBaseTemplateTag;
     */
    public function day($options = array())
    {
        $options = new KConfig($options);

        $options->append(array(
            'selected' => null,
            'prompt' => true,
        ));

        $days = array_combine(range(1, 31), range(1, 31));
        $selected = $options->selected;
        unset($options->selected);
        $prompt = $options->prompt;
        unset($options->prompt);
        if ($prompt) {
            $array = array(AnTranslator::_('LIB-AN-SELECTOR-SELECT-DAY'));
            $days = AnHelperArray::merge($array, $days);
        }

        return $this->_html->select($options->name, array('options' => $days, 'selected' => $selected), KConfig::unbox($options));
    }

    /**
     * Return a selection of countries.
     *
     * @param array $options
     *
     * @return LibBaseTemplateTag
     */
    public function country($options = array())
    {
        $options = new KConfig($options);

        $options->append(array(
            'id' => 'an-se-selection-countries-'.uniqid(),
            'selected' => null,
            'name' => 'country',
            'use_country_code' => true,
            'prompt' => true,
        ));

        $selected = $options->selected;
        $use_country_code = $options->use_country_code;

        unset($options->selected);
        unset($options->use_country_code);

        $countries = array(
            null => AnTranslator::_('LIB-AN-SELECTOR-SELECT-OPTION'),
        );

        foreach (self::$COUNTRIES as $key => $name) {
            $countries[$use_country_code ? $key : $name] = AnTranslator::_($name);
        }

        return $this->_html->select($options->name, array('options' => $countries, 'selected' => $selected), KConfig::unbox($options));
    }

    /**
     * Return a selection of states or provinces for canada.
     *
     * @param array $options
     *
     * @return LibBaseTemplateTag
     */
    public function state($options = array())
    {
        $options = new KConfig($options);

        $options->append(array(
            'country_selector' => 'countery-selector',
            'country' => 'US',
            'selected' => null,
            'use_state_code' => true,
            'prompt' => true,
        ));

        $country = strtolower($options->country);

        $country_selector = null;

        if ($options->country_selector) {
            $country_selector = $options->country_selector;
        }

        $use_state_code = $options->use_state_code;

        unset($options->use_state_code);
        unset($options->country);
        unset($options->country_selector);

        $states = array();
        $provinces = array();

        foreach (self::$US_STATES as $key => $name) {
            $states[$use_state_code ? $key : $name] = AnTranslator::_($name);
        }

        foreach (self::$CANADA_PROVINCES as $key => $name) {
            $provinces[$use_state_code ? $key : $name] = AnTranslator::_($name);
        }

        $selected = $options->selected;

        unset($options->selected);

        $options = KConfig::unbox($options);

        $states = $this->_html->select($options['name'], array('options' => $states, 'selected' => $selected), array_merge($options, array('country' => 'us')));
        $provinces = $this->_html->select($options['name'], array('options' => $provinces, 'selected' => $selected), array_merge($options, array('country' => 'canada')));
        $custom = $this->_html->textfield($options['name'], $selected, array_merge($options, array('country' => 'custom')));

        if (empty($country_selector)) {
            return $country == 'us' ? $states : $provinces;
        }

        return $states.$provinces.$custom;
    }

    /**
     * Return a selection of standard currency codes.
     *
     * @param array $options
     *
     * @return LibBaseTemplateTag
     */
    public function currency($options = array())
    {
        $options = new KConfig($options);

        $options->append(array(
            'name' => 'currency',
            'prompt' => true,
        ));

        $selected = $options->selected;
        unset($options->selected);
        $currencies = array_combine(self::$ISO_CURRENCY_SYMBOLS, self::$ISO_CURRENCY_SYMBOLS);

        return $this->_html->select($options->name, array('options' => $currencies, 'selected' => $selected), KConfig::unbox($options));
    }
}
