<?php

/**
 * Form Helper.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComBaseTemplateHelperForm extends LibBaseTemplateHelperForm
{
    /**
     * Render a parameter.
     *
     * @param JParameter $parameter
     * @param KConfig    $config
     *
     * @return string
     */
    protected function _render($parameter, $config)
    {
        $params = $parameter->getParams($config->name, $config->group);
        foreach ($params as $key => $param) {
            $params[$key] = array($param[0] => $param[1]);
        }

        return $this->_template->renderHelper('ui.form', $params[0]);
    }
}
