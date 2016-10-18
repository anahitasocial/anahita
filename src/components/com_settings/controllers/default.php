<?php

/**
 * Global Config Controller.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008-2016 rmd Studio Inc.
 * @license    GNU GPLv3
 *
 * @link       http://www.GetAnahita.com
 */

class ComSettingsControllerDefault extends ComBaseControllerResource
{
  /**
   * Initializes the options for the object.
   *
   * Called from {@link __construct()} as a first step of object instantiation.
   *
   * @param 	object 	An optional KConfig object with configuration options.
   */
  protected function _initialize(KConfig $config)
  {
      $config->append(array(
          'toolbars' => array($this->getIdentifier()->name, 'menubar'),
          'request' => array(
              'limit' => 20
          ),
      ));

      parent::_initialize($config);
  }

}
