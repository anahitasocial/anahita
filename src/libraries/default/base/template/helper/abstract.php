<?php
/**
 * @category   Anahita
 *
 * @author	   Johan Janssens <johan@nooku.org>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright  Copyright (C) 2018 rmd Studio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
abstract class LibBaseTemplateHelperAbstract extends KObject implements LibBaseTemplateHelperInterface
{
    /**
     * Template object
     *
     * @var	object
     */
    protected $_template;

    /**
     * Constructor
     *
     * Prevent creating instances of this class by making the contructor private
     *
     * @param 	object 	An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        // Set the view indentifier
        $this->_template = $config->template;
    }

    /**
     * Get the template object
     *
     * @return  object	The template object
     */
    public function getTemplate()
    {
        return $this->_template;
    }
}
