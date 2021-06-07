<?php

/**
 * Converts @username terms to links.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class PlgContentfilterMention extends PlgContentfilterAbstract
{
    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'priority' => AnCommand::PRIORITY_LOW,
        ));

        parent::_initialize($config);
    }

    /**
     * Filter a value.
     *
     * @param string The text to filter
     *
     * @return string
     */
    public function filter($text)
    {
        $matches = array();

        $text = preg_replace(
            ComPeopleDomainEntityPerson::PATTERN_MENTION,
            '<a class="mention" href="'.route('option=com_people&view=person&uniqueAlias=$1').'">$0</a>',
            $text);

        return $text;
    }
}
