<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Description
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Contains the inheritance information 
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Description
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnDomainDescriptionInheritance
{
    /**
     * Inheritance tree
     * 
     * @var array
     */
    protected $_tree;
    
    /**
     * The identifier used in the inheritance information. It's the same identifier as the
     * entity but without the application. An identifier must be of 
     * format com:[component].domain.entity.[name]
     *
     * @var string
     */
    protected $_identifier;
    
    /**
     * Constuctor the inheritance object
     *
     * @param array $tree                    Array of inheritance tree
     * @param KServiceIdentifier $identifier The entity identifier
     * 
     * @return AnDomainDescriptionInheritance
     */
    public function __construct(array $tree, $identifier = null)
    {
        $this->_tree = $tree;        
                
        $this->_identifier = $identifier;
    }
    
    /**
     * Returns the inheritance tree
     * 
     * @return array
     */
    public function getTree()
    {
        return $this->_tree;
    }
    
    /**
     * Returns the inheritance tree
     *
     * @return KServiceIdentifier
     */
    public function getIdentifier()
    {
        return $this->_identifier;
    }
    
    /**
     * String representation of an inheritance. It's in the format of 
     * parent1,parent2,...,parentn,identifier
     *
     * @return string
     */
    public function __toString()
    {
        //it's the base class so there are no inheritance value
        if ( empty($this->_tree) ) {
            $string = '';
        }
        else {
            $string   = implode(',', $this->_tree);
            $string  .= ','.$this->_identifier;
        }
        return $string;
    }
}