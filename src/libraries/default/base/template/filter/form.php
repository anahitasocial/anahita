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
class LibBaseTemplateFilterForm extends LibBaseTemplateFilterAbstract implements LibBaseTemplateFilterWrite
{
    /**
     * The form token value
     *
     * @var string
     */
    protected $_token_value;
    
    /**
     * The form token name
     *
     * @var string
     */
    protected $_token_name;
    
    /**
     * Constructor.
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config = null)
    {
        parent::__construct($config);
        
        $this->_token_value = $config->token_value;
        $this->_token_name  = $config->token_name;
    }
    
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'token_value' => '',
            'token_name' => '_token',
        ));

        parent::_initialize($config);
    }
    
    /**
     * Get the session token value.
     *
     * If a token isn't set yet one will be generated. Tokens are used to secure forms
     * from spamming attacks. Once a token has been generated the system will check the
     * post request to see if it is present, if not it will invalidate the session.
     *
     * @param boolean If true, force a new token to be created
     * @return string The session token
     */
    protected function _tokenValue($force = false)
    {
        return $this->_token_value;
    }
    
    /**
     * Get the session token name
     *
     * Tokens are used to secure forms from spamming attacks. Once a token
     * has been generated the system will check the post request to see if
     * it is present, if not it will invalidate the session.
     *
     * @return string The session token
     */
    protected function _tokenName()
    {
        return $this->_token_name;
    }
    
    /**
     * Add unique token field
     *
     * @param string
     * @return LibBaseTemplateFilterForm
     */
    public function write(&$text)
    {
        // All: Add the action if left empty
        if (preg_match_all('#<\s*form.*?action=""#im', $text, $matches, PREG_SET_ORDER)) {
            $view   = $this->getTemplate()->getView();
            $state  = $view->getModel()->getState();
            $action = $view->getRoute(http_build_query($state->getData($state->isUnique())));
            
            foreach ($matches as $match) {
                $str  = str_replace('action=""', 'action="'.$action.'"', $match[0]);
                $text = str_replace($match[0], $str, $text);
            }
        }
        
        // POST : Add token
        $matches = array();
        preg_match_all('/(<form.*method="post".*>)/i', $text, $matches, PREG_SET_ORDER);
       
        foreach ($matches as $match) {
            $input = PHP_EOL.'<input type="hidden" name="'.$this->_tokenName().'" value="'.$this->_tokenValue().'" />';
            $text = str_replace($match[0], $match[0].$input, $text, $count);
        }
       
        // GET : Add token to .-koowa-grid forms
        $matches = array();
        preg_match_all('#(<\s*?form\s+?.*?class=(?:\'|")[^\'"]*?-koowa-grid.*?(?:\'|").*?)#im', $text, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $input = ' data-token-name="'.$this->_tokenName().'" data-token-value="'.$this->_tokenValue().'"';
            $text= str_replace($match[0], $match[0].$input, $text, $count);
        }
        
        // GET : Add query params
        $matches = array();
        if (preg_match_all('#<form.*action=".*\?(.*)".*method="get".*>#iU', $text, $matches)) {
            foreach ($matches[1] as $key => $query) {
                parse_str(str_replace('&amp;', '&', $query), $query);
                
                $input = $this->_renderQuery($query);
                $text  = str_replace($matches[0][$key], $matches[0][$key].$input, $text);
            }
        }
        
        return $this;
    }
    
    /**
     * Recursive function that transforms the query array into a string of input elements
     *
     * @param 	array	Associative array of query information
     * @param 	string	The name of the current input element
     * @return 	string	String of the html input elements
     */
    protected function _renderQuery($query, $key = '')
    {
        $input = '';
        foreach ($query as $name => $value) {
            $name = $key ? $key.'['.$name.']' : $name;
            if (is_array($value)) {
                $input .= $this->_renderQuery($value, $name);
            } else {
                $input .= PHP_EOL.'<input type="hidden" name="'.$name.'" value="'.$value.'" />';
            }
        }
        
        return $input;
    }
}
