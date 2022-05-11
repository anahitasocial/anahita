<?php


 /**
  * Base Template.
  *
  * @category   Anahita
  *
  * @author     Arash Sanieyan <ash@anahitapolis.com>
  * @author     Rastin Mehr <rastin@anahita.io>
  * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
  *
  * @link       http://www.Anahita.io
  */
 class ComBaseTemplateDefault extends LibBaseTemplateDefault
 {
     /**
     * Constructor.
     *
     * @param 	object 	An optional AnConfig object with configuration options
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        if ($config->cache) {
            $this->_paths = $this->getService('application.registry', array('key' => 'template-paths-'.$config->cache_key));
            $this->_parsed_data = $this->getService('application.registry', array('key' => 'template-parsed-data-'.$config->cache_key));
        }
    }

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
            'cache' => true,
            'cache_key' => (string) $this->getIdentifier(),
        ));

        parent::_initialize($config);
    }
 }
