<?php
/**
 * @version		$Id: csv.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package     Koowa_View
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Export a view as a CSV file
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_View
 */
class KViewCsv extends KViewFile
{
	/**
	 * Character used for quoting
	 *
	 * @var string
	 */
	public $quote = '"';

	/**
	 * Character used for separating fields
	 *
	 * @var string
	 */
	public $separator = ',';

	/**
	 * End of line
	 *
	 * @var string
	 */
	public $eol = "\n";

	/**
	 * Initializes the options for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 * @return  void
	 */
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'version'     => '1.0',
			'disposition' => 'inline',
			'quote'		  => '"',
			'separator'	  => ',',
			'eol'		  => "\n"
       	))->append(array(
			'mimetype'	  => 'text/csv; version='.$config->version,
		));

       	parent::_initialize($config);
    }

	/**
	 * Return the views output
 	 *
	 *  @return string 	The output of the view
	 */
	public function display()
	{
		$rows    = '';
	    $columns = array();
		$rowset  = $this->getModel()->getList();

		// Get the columns
		foreach($rowset as $row)
		{
			$data    = $row->toArray();
		    $columns = array_merge($columns + array_flip(array_keys($data)));
		}

		// Empty the column values
		foreach($columns as $key => $value) {
		    $columns[$key] = '';
		}

		//Create the rows
	    foreach($rowset as $row)
		{
			$data = $row->toArray();
		    $data = array_merge($columns, $data);

		    $rows .= $this->_arrayToString(array_values($data)).$this->eol;
		}

		// Create the header
		$header = $this->_arrayToString(array_keys($columns)).$this->eol;

		// Set the output
		$this->output = $header.$rows;

		return parent::display();
	}

	/**
     * Render
     *
     * @param	string	Value
     * return 	boolean
     */
	protected function _arrayToString($data)
    {
    	$fields = array();
        foreach($data as $value)
        {
            //Implode array's
            if(is_array($value)) {
    	        $value = implode(',', $value);
    	    }

    	     // Escape the quote character within the field (e.g. " becomes "")
            if ($this->_quoteValue($value))
            {
                $quoted_value = str_replace($this->quote, $this->quote.$this->quote, $value);
                $fields[] 	  = $this->quote . $quoted_value . $this->quote;
            }
            else $fields[] = $value;
        }

        return  implode($this->separator, $fields);
    }

    /**
     * Check if the value should be quoted
     *
     * @param	string	Value
     * return 	boolean
     */
    protected function _quoteValue($value)
    {
        if(is_numeric($value)) {
    		return false;
    	}

        if(strpos($value, $this->separator) !== false) { // Separator is present in field
        	return true;
        }

        if(strpos($value, $this->quote) !== false) { // Quote character is present in field
        	return true;
        }

        if (strpos($value, "\n") !== false || strpos($value, "\r") !== false ) { // Newline is present in field
        	return true;
        }

        if(substr($value, 0, 1) == " " || substr($value, -1) == " ") {  // Space found at beginning or end of field value
        	return true;
        }

        return false;
    }
}