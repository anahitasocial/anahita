<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage View
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * File View
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage View
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseViewFile extends LibBaseViewFile
{	
	/**
	 * Return the views output
 	 *
	 *  @return string 	The output of the view
	 */
    public function display()
    {
    	if ( $this->entity && $this->entity->isFileable() ) 
    	{
    		$this->output   = $this->entity->getFileContent();
    		$this->filename = $this->entity->getFileName();
    	}
    	return parent::display();
    }
}