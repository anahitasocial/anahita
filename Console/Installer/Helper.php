<?php

/**
 * Moved JInstallerHelper
 */
class AnInstallationHelper
{
	/**
	 * Creates a new database
	 * @param object Database connector
	 * @param string Database name
	 * @param boolean utf-8 support
	 * @param string Selected collation
	 * @return boolean success
	 */
	static public function createDatabase(& $db, $DBname, $DButfSupport)
	{
		if ($DButfSupport) {
			$sql = "CREATE DATABASE `$DBname` CHARACTER SET `utf8`";
		} else {
			$sql = "CREATE DATABASE `$DBname`";
		}

		$db->setQuery($sql);
		$db->query();
		$result = $db->getErrorNum();

		if ($result != 0) {
			return false;
		}

		return true;
	}

	/**
	 * Return whether the any tables exists or not
	 *
	 * @param object $db
	 *
	 * @return boolean
	 */
	static public function databaseExists(&$db, $name)
	{
	    if (!$db->select($name)) {
	        return false;
	    }

	    $query = "SHOW TABLES FROM `$name`";
			$db->setQuery($query);
			$errors = array ();

			if ($tables = $db->loadResultArray()) {
				foreach ($tables as $table) {
					if (strpos($table, $db->getPrefix()) === 0) {
							return true;
					}
				}
			}

			return false;
	}

	/**
	 * Deletes all database tables
	 * @param object Database connector
	 * @param array An array of errors encountered
	 */
	static public function deleteDatabase(& $db, $DBname, $DBPrefix, & $errors)
	{
			$query = "SHOW TABLES FROM `$DBname`";
			$db->setQuery($query);
			$errors = array ();

			if ($tables = $db->loadResultArray()) {
				foreach ($tables as $table) {
					if (strpos($table, $DBPrefix) === 0) {

						$query = "DROP TABLE IF EXISTS `$table`";
						$db->setQuery($query);
						$db->query();

						if ($db->getErrorNum()) {
							$errors[$db->getQuery()] = $db->getErrorMsg();
						}
					}
				}
			}

			return count($errors);
	}

	/**
	 *
	 */
	static public function populateDatabase(&$db, $sqlfile, & $errors, $nexttask='mainconfig')
	{
			if(!($buffer = file_get_contents($sqlfile))) {
					return -1;
			}

			$queries = AnInstallationHelper::splitSql($buffer);

			foreach ($queries as $query) {

					$query = trim($query);

					if ($query != '' && $query {0} != '#') {

							$db->setQuery($query);
							//echo $query .'<br />';
							$db->query() or die($db->getErrorMsg());

							AnInstallationHelper::getDBErrors($errors, $db );
					}
			}

			return count($errors);
	}

	/**
	 * @param string
	 * @return array
	 */
	static public function splitSql($sql)
	{

		$sql = trim($sql);
		$sql = preg_replace("/\n\#[^\n]*/", '', "\n".$sql);
		$buffer = array ();
		$ret = array ();
		$in_string = false;

		for ($i = 0; $i < strlen($sql) - 1; $i ++) {

			if ($sql[$i] == ";" && !$in_string) {
				$ret[] = substr($sql, 0, $i);
				$sql = substr($sql, $i +1);
				$i = 0;
			}

			if ($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\") {
				$in_string = false;
			} elseif (!$in_string && ($sql[$i] == '"' || $sql[$i] == "'") && (!isset ($buffer[0]) || $buffer[0] != "\\")) {
					$in_string = $sql[$i];
			}

			if (isset ($buffer[1])) {
					$buffer[0] = $buffer[1];
			}

			$buffer[1] = $sql[$i];
		}

		if (!empty ($sql)) {
			$ret[] = $sql;
		}

		return ($ret);
	}

	static public function return_bytes($val) {

		$val = trim($val);
		$last = strtolower($val{strlen($val)-1});

		switch($last) {
			// The 'G' modifier is available since PHP 5.1.0
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}

		return $val;
	}

	static public function replaceBuffer(&$buffer, $oldPrefix, $newPrefix, $srcEncoding) {

			$buffer = str_replace( $oldPrefix, $newPrefix, $buffer );

			/*
			 * convert to utf-8
			 */
			if (function_exists('iconv')) {
				$buffer = iconv( $srcEncoding, 'utf-8//TRANSLIT', $buffer );
			}
	}

	static public function appendFile(&$buffer, $filename) {
			$fh = fopen($filename, 'a');
			fwrite($fh, $buffer);
			fclose($fh);
	}

	static public function isValidItem( $link, $lookup )
	{
			foreach ($lookup as $component) {
				if (strpos( $link, $component ) != false) {
						return true;
				}
			}

			return false;
	}

	static public function getDBErrors( & $errors, $db )
	{
			if ($db->getErrorNum() > 0) {
					$errors[] = array('msg' => $db->getErrorMsg(), 'sql' => $db->_sql);
			}
	}
}
