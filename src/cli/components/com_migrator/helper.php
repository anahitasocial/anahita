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
        $queries = str_replace('jos_','#__',$sql);
        try
        {
            $then = microtime();
            $db->execute($sql);
            $diff = microtime() - $then;
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
function dbparse($data)
{
    $queries = explode(";",$data);
    $array   = array();
    foreach($queries as $key => $value)
    {
        $value = trim($value);
        $value = preg_replace('/^\n/','',preg_replace('/^\/\/.*/','',$value));
        $value = preg_replace('/ +/',' ',str_replace("\n",' ',$value));
        if ( !preg_match('/\S/', $value) ) 
        {
            continue;
        }
        $array[] = $value.';';
    }
    return $array;
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