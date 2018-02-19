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
class LibBaseTemplateHelperDate extends LibBaseTemplateHelperAbstract implements KServiceInstantiatable
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
     * current time.
     *
     * @var AnDate
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
     * Date picker. Return a selcetor with all the date components.
     *
     * @param string $name
     * @param AnDate  $date
     * @param array  $options
     */
    public function picker($name, $options = array())
    {
        $options = new KConfig($options);

        $options->append(array(
            'date' => new AnDate(),
        ));

        $date = $options->date;

        $html = $this->getService('com:base.template.helper.html');

        if (is_string($date)) {
            $date = new AnDate(new KConfig(array('date' => $date)));
        }

        $month = $date->month;
        $year = $date->year;
        $day = $date->day;

        $months = array(
            0 => 'Select Month',
            1 => AnTranslator::_('JANUARY') ,
            2 => AnTranslator::_('FEBRUARY') ,
            3 => AnTranslator::_('MARCH')  ,
            4 => AnTranslator::_('APRIL')  ,
            5 => AnTranslator::_('MAY')    ,
            6 => AnTranslator::_('JUNE')   ,
            7 => AnTranslator::_('JULY')    ,
            8 => AnTranslator::_('AUGUST') ,
            9 => AnTranslator::_('SEPTEMBER') ,
            10 => AnTranslator::_('OCTOBER') ,
            11 => AnTranslator::_('NOVEMBER') ,
            12 => AnTranslator::_('DECEMBER') ,
        );

        $days = array(0 => 'Select Day');
        $years = array(0 => 'Select Year');

        foreach (range(1, 31) as $i => $num) {
            $days[$i + 1] = $num;
        }

        $current = new AnDate();

        foreach (range(0, 100) as $i) {
            $years[$current->year + $i] = $current->year + $i;
        }

        $year = $html->select($name.'[year]', array('options' => $years,  'selected' => $year))->class('input-medium');
        $month = $html->select($name.'[month]', array('options' => $months, 'selected' => $month))->class('input-medium');
        $day = $html->select($name.'[day]', array('options' => $days,   'selected' => $day))->class('input-small');

        return $year.' '.$month.' '.$day;
    }

    /**
     * Return a human friendly format of the date.
     *
     * @param AnDate $date
     * @param array $config Optional array to pass format
     *
     * @return string
     */
    public function humanize($date, $config = array())
    {
        $config = new KConfig($config);

        $config->append(array(
            'format' => '%B %d %Y',
            'relative' => false,
            'offset' => null,
        ));

        $format = $config->format;

        $diff = $this->_current_time->getDate(DATE_FORMAT_UNIXTIME) - $date->getDate(DATE_FORMAT_UNIXTIME);

        if ($config->relative) {
            $timeLeft = ($diff < 0) ? '-FUTURE' : '';

            $diff = abs($diff);

            if ($diff < 1) {
                return sprintf(AnTranslator::_('LIB-AN-DATE-MOMENT'), $diff);
            }

            if ($diff < 60) {
                return ($diff > 1) ? sprintf(AnTranslator::_('LIB-AN-DATE-SECONDS'.$timeLeft), $diff) : sprintf(AnTranslator::_('LIB-AN-DATE-SECOND'.$timeLeft), $diff);
            }

            $diff = round($diff / 60);

            if ($diff < 60) {
                return ($diff > 1) ? sprintf(AnTranslator::_('LIB-AN-DATE-MINUTES'.$timeLeft), $diff) : sprintf(AnTranslator::_('LIB-AN-DATE-MINUTE'.$timeLeft), $diff);
            }

            $diff = round($diff / 60);
            if ($diff < 24) {
                return ($diff > 1) ? sprintf(AnTranslator::_('LIB-AN-DATE-HOURS'.$timeLeft), $diff) : sprintf(AnTranslator::_('LIB-AN-DATE-HOUR'.$timeLeft), $diff);
            }

            $diff = round($diff / 24);
            if ($diff < 7) {
                return ($diff > 1) ? sprintf(AnTranslator::_('LIB-AN-DATE-DAYS'.$timeLeft), $diff) : sprintf(AnTranslator::_('LIB-AN-DATE-DAY'.$timeLeft), $diff);
            }

            $diff = round($diff / 7);

            if ($diff < 4) {
                return ($diff > 1) ? sprintf(AnTranslator::_('LIB-AN-DATE-WEEKS'.$timeLeft), $diff) : sprintf(AnTranslator::_('LIB-AN-DATE-WEEK'.$timeLeft), $diff);
            }
        } elseif ($config->offset) {
            $date->addHours($config->offset);
        }

        return $date->getDate($format);
    }
}
