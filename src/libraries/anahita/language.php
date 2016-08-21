<?php

/**
 * Language handling class
 *
 * @static
 * @package 	Anahita.Framework
 * @subpackage	Language
 * @since		4.3
 */
class AnLanguage extends KObject
{
    /**
    *   Debug mode
    */
    protected $_debug = false;

    /**
    *   Default language
    */
    protected $_default = 'en-GB';

    /**
    *   Array of orphan text
    */
    protected $_orphans = array();

    /**
    *   Language meta data
    */
    protected $_meta = null;

    /**
    *   Selected language
    */
    protected $_language = null;

    /**
    *   List of language files that have been loade
    */
    protected $_paths = array();

    /**
    *   Array of translations
    */
    protected $_strings = array();

    /**
    *   Array of used text
    */
    protected $_used = array();

    
}
