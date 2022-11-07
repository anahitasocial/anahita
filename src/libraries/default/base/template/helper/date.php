<?php
/**
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class LibBaseTemplateHelperDate extends LibBaseTemplateHelperAbstract implements AnServiceInstantiatable
{
    /**
     * Force creation of a singleton.
     *
     * @param AnConfigInterface  $config    An optional AnConfig object with configuration options
     * @param AnServiceInterface $container A AnServiceInterface object
     *
     * @return AnServiceInstantiatable
     */
    public static function getInstance(AnConfigInterface $config, AnServiceInterface $container)
    {
        if (! $container->has($config->service_identifier)) {
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
     * @param 	object 	An optional AnConfig object with configuration options
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        //load the com_Anahita_SocialCore language for the date related string
        $this->_current_time = AnDomainAttributeDate::getInstance();
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
        $config = new AnConfig($config);

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
