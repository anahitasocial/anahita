<?php
/**
 * @package     Anahita_Database
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright   Copyright (C) 2018 Rastin Mehr. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 * @link        https://www.GetAnahita.com
 */
class AnDatabaseSchemaTable extends AnObject
{
    /**
     * Table name
     *
     * @var string
     */
    public $name;
    
    /**
     * The storage engine
     *
     * @var string
     */
    public $engine;
    
    /**
     * Table type
     *
     * @var	string
     */
    public $type;
    
    /**
     * Table length
     *
     * @var integer
     */
    public $length;
    
    /**
     * Table next auto increment value
     *
     * @var integer
     */
    public $autoinc;
    
    /**
     * The tables character set and collation
     *
     * @var string
     */
    public $collation;
    
    /**
     * The tables description
     *
     * @var string
     */
    public $description;
    
    /**
     * List of columns
     *
     * Associative array of columns, where key holds the columns name and the value is
     * an AnDatabaseSchemaColumn object.
     *
     * @var	array
     */
    public $columns = array();
        
    /**
     * List of indexes
     *
     * Associative array of indexes, where key holds the index name and the
     * and the value is an object.
     *
     * @var	array
     */
    public $indexes = array();
}
