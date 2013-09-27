<?php

/**
 * Output a DB result
 *
 * @return void
 */
function dboutput($output)
{
    if(php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR'])) 
        print $output;    
}

/**
 * Helper method to return a MYSQL nested replace method
 *
 * @param string $column The column to perform replace on
 * @param array  $array  The array of nested replace
 * 
 * @return string
 */
function dbreplace_func($column, $array)
{
    $statements = array();
    foreach($array as $key => $value)
    {
        $column  = "REPLACE($column,'$key','$value')";     
    }   
    return $column; 
}

/**
 * Executes a query
 *
 * @param string  $queries The query to execute
 * @param boolean force    If force set to through then no exception is raised. Just an error log
 * 
 * @return mixed
 */
function dbexec($queries, $force = true)
{
    settype($queries, 'array');
    $db      = KService::get('koowa:database.adapter.mysqli');
    foreach($queries as $sql)
    {
        $sql = str_replace('jos_','#__',$sql);
        try
        {
            $then = microtime(true);
            $db->execute($sql);
            $diff = microtime(true) - $then;
            dboutput("QUERY: ".$sql." ($diff)"."\n");
        } catch(Exception $e)
        {
            if ( $force ) {
                dboutput("QUERY ERROR IGNORED: ".$e->getMessage()."\n");
            } else {
                dboutput("QUERY ERROR: ".$sql."\n");
                throw $e;
            }
        }
    }
    return true;
}

/**
 * Inserts a row/rowset
 *
 * @param string $table Table name
 * @param array  $data  The data
 *
 * @return mixed
 */
function dbinsert($table, $data)
{
    func_get_args();
    func_num_args();
    if ( func_num_args() > 2 )
    {
        $keys    = $data;
        $data    = array_slice(func_get_args(),2); 
        $array   = array();        
        foreach($data as $i => $values)
        {
            $array[$i] = array_combine($keys, $values);            
        }
        $data = $array;
    }
    elseif ( !is_numeric(key($data)) )
    {
        $data = array($data);
    }

    $db   = KService::get('koowa:database.adapter.mysqli');
    $keys = array_keys($data[0]);    
    foreach($keys as $i => $key)
    {
        $keys[$i] = $db->quoteName($key);
    }
    $query = "INSERT INTO jos_$table (".implode(',', $keys).") VALUES\n";
    $keys  =  array_keys($data[0]);
    $inserts = array();
    foreach($data as $values) 
    {
        $array = array();
        foreach($keys as $key)
        {
            $value = @$values[$key];
            if ( is_null($value) )
                $array[] = 'NULL';
            else
                $array[] = $db->quoteValue($value);                        
        }       
        
       $inserts[] = '('.implode(',', $array).')'."\n";
    }
    $query .= implode(',', $inserts);
    
    global $db_return_string;
    
    if ( $db_return_string )
    {
        $db_return_string = false;
        return $query;
    }
        
    try 
    {
        dbexec($query);
    }
    catch(Exception $e)
    {
        print_r($array);
        throw $e;
    }
}

/**
 * Fetches a row of data from the database
 *
 * @param string  $select The Select query
 * @param int     $mode   The fetch mode. @see KDatabase
 * 
 * @return mixed
 */
function dbfetch($select, $mode = KDatabase::FETCH_ARRAY_LIST)
{
    $select = str_replace('jos_','#__',$select);
    $db  = KService::get('koowa:database.adapter.mysqli');
    $then   = microtime();
    $result = $db->select($select, $mode);
    $diff   = microtime() - $then;
    dboutput("QUERY: ".$select." ($diff)"."\n");
    return $result;    
}

/**
 * Parse Queries using the ;
 *
 * @param string  $data The query to parse 
 *
 * @return array
 */
function dbparse($sql)
{
    $sql = trim($sql);
	$sql = preg_replace("/\n\#[^\n]*/", '', "\n".$sql);
	$buffer = array ();
	$ret = array ();
	$in_string = false;

	for ($i = 0; $i < strlen($sql) - 1; $i ++) {
		if ($sql[$i] == ";" && !$in_string)
		{
			$ret[] = substr($sql, 0, $i);
			$sql = substr($sql, $i +1);
			$i = 0;
		}

		if ($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\")
		{
			$in_string = false;
		}
		elseif (!$in_string && ($sql[$i] == '"' || $sql[$i] == "'") && (!isset ($buffer[0]) || $buffer[0] != "\\"))
		{
			$in_string = $sql[$i];
		}
		if (isset ($buffer[1]))
		{
			$buffer[0] = $buffer[1];
		}
		$buffer[1] = $sql[$i];
	}

	if (!empty ($sql))
	{
		$ret[] = $sql;
	}
	return ($ret);
}

/**
 * executes a sql file
 * 
 * @param string $file
 */
function dbexecfile($file)
{
    $queries = dbparse(file_get_contents($file));
    dbexec($queries);
}

/**
 * Check if a table exists
 *
 * @param string $table
 * 
 * @return boolean
 */
function dbtable_exists($table)
{
    $table = '#__'.$table;
    return dbexists("SHOW TABLES LIKE '$table'");
}

/**
 * Returns whether there at least one row with select query
 *
 * @param string $select The Select query
 *
 * @return boolean
 */
function dbexists($select)
{
    return !is_null(dbfetch($select, KDatabase::FETCH_FIELD));
}