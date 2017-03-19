<?php

/**
 * Post filter.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComMediumFilterHtml extends KFilterHtml
{
    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            //whilte list these tags
            'tag_method' => 0,
            'tag_list' => array('p', 'strike', 'u', 'pre', 'address', 'blockquote', 'b', 'i', 'ul', 'ol', 'li', 'h1', 'h2', 'h3', 'h4', 'h5'),
        ));

        if ($config->tag_list) {
            $config['tag_list'] = KConfig::unbox($config->tag_list);
        }

        if ($config->tag_method) {
            $config['tag_method'] = KConfig::unbox($config->tag_method);
        }

        $config['attribute_method'] = 0;
        $config['attribute_list'] = array();

        parent::_initialize($config);
    }

    /**
     * Sanitize a value
     *
     * @param   scalar  Input string/array-of-string to be 'cleaned'
     * @return  mixed   'Cleaned' version of input parameter
     */
    protected function _sanitize($value)
    {
        //strip php tags
        $value = preg_replace('/<\\?.*(\\?>|$)/Us', '', $value);
        $value = parent::_sanitize($value);

        return $value;
    }
}
