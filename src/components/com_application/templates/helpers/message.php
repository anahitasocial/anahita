<?php

/**
 * Renders a message. This class is specialized at the application
 * level to render system messages.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComApplicationTemplateHelperMessage extends LibBaseTemplateHelperAbstract
{
    /**
     * Renders a message using the passed configuration.
     *
     * @param string $message The message to render
     * @param array  $config  Message configuration
     *
     * @return string
     */
    public function render($config = array())
    {
        $message = $config['message'];

        unset($config['message']);

        $message = '<p>'.$message.'</p>';
        $buttons = isset($config['buttons']) ? $config['buttons'] : array();

        unset($config['buttons']);

        if (! empty($buttons)) {
            foreach ($buttons as $label => $attrbs) {
                $buttons[$label] = $this->_renderButton($label, $attrbs);
            }

            $message .= '<p class="alert-actions">'.implode(' ', $buttons).'</p>';
        }

        return $this->_renderMessage($message, $config);
    }

    /**
     * Renders an individual message.
     *
     * @param string $message The message to render
     * @param array  $config  Options
     *
     * @return string
     */
    protected function _renderMessage($message, $config)
    {
        $config = new KConfig($config);

        $config->append(array(
            'type' => 'info',
        ));

        $message = ''
        .'<div class="alert alert-block alert-'.$config->type.'">'
        .'<a class="close" data-dismiss="alert" href="#">&times;</a>'
        .$message
        .'</div>';

        return $message;
    }

    /**
     * Renders a button for a message.
     *
     * @param string $label  Button label
     * @param array  $attrbs Button attributes
     *
     * @return string
     */
    protected function _renderButton($label, $attrbs)
    {
        $attrbs = new KConfig($attrbs);

        $attrbs->append(array(
            'class' => '',
        ));

        $attrbs->class .= 'btn small';

        if (isset($attrbs->type)) {
            $attrbs->class .= ' btn-'.$attrbs->type;

            unset($attrbs->type);
        }

        $attrbs = KConfig::unbox($attrbs);

        foreach ($attrbs as $key => $value) {
            $attrbs[$key] = $key.'="'.$value.'"';
        }

        $attrbs = implode(' ', $attrbs);

        return '<a '.$attrbs.'>'.$label.'</a>';
    }
}
