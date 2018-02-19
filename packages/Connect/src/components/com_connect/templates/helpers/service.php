<?php


 /**
  * Connect Helper Class.
  *
  * @category   Anahita
  *
  * @author     Arash Sanieyan <ash@anahitapolis.com>
  * @author     Rastin Mehr <rastin@anahitapolis.com>
  * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
  *
  * @link       http://www.GetAnahita.com
  */
 class ComConnectTemplateHelperService extends LibBaseTemplateHelperAbstract
 {
     /**
     * Render logins, If $return_array is set to true, then it returns an array instead of a string.
     *
     * @param bool $return_array Boolean value to whether return an array of string or one string. By default is false
     *
     * @return string
     */
    public function renderLogins($return_array = false)
    {
        $services = ComConnectHelperApi::getServices();

        $html = array();

        foreach ($services as $name => $service) {
            $html[] = $this->login($name);
        }

        return $return_array ? $html : implode('&nbsp;', $html);
    }

    /**
     * Returns the logo for a service.
     *
     * @param string $service
     * @param array  $config
     *
     * @return LibBaseTemplateHelperHtmlElement
     */
    public function login($service, $config = array())
    {
        $config = new KConfig($config);

        $config->append(array(
            'html' => $this->icon($service),
            'url' => $this->url($service),
            'attributes' => array(),
        ));

        $html = $this->getService('com:base.template.helper.html');

        $link = $html->link($config->html, $config->url, $config->attributes)
                     ->class('btn btn-'.$service)
                     ->dataTrigger('PostLink')
                     ->title($service);

        return $link;
    }

    /**
     * Returns the url login for a service.
     *
     * @param string $service
     *
     * @return string
     */
    public function url($service)
    {
        $url = 'index.php?option=com_connect&view=login&server='.$service;

        if (KRequest::get('get.return', 'cmd')) {
            $url .= '&return='.KRequest::get('get.return', 'raw');
        }

        return route($url);
    }

    /**
     * Returns the icons for a service.
     *
     * @param string $service
     *
     * @return LibBaseTemplateHelperHtmlElement
     */
    public function icon($service)
    {
        return '<i class="icon-'.$service.'"></i>';
    }
 }
