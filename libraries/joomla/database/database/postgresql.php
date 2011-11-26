<?php
/**
 * @package		Joomla.Platform
 * @subpackage	Database
 * 
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JDatabaseQueryPostgreSQL', dirname(__FILE__).'/postgresqlquery.php');

/**
 * PostgreSQL database driver
 *
 * @package		Joomla.Framework
 * @subpackage	Database
 * @since		11.1
 */
class JDatabasePostgreSQL extends JDatabase
{
	/**
	 * The database driver name
	 *
	 * @var string
	 */
	public $name = 'postgresql';

	/**
	 *  The null/zero date string
	 *
	 * @var string
	 */
	protected $nullDate = 'epoch';

	/**
	 * Quote for named objects
	 *
	 * @var string
	 */
	protected $nameQuote = '\'';

	/**
	 * Operator used for concatenation
	 *
	 * @var string
	 */
	protected $concat_operator = '||';

	/**
	 * Database object constructor
	 *
	 * @param	array	List of options used to configure the connection
	 * @since	11.1
	 * @see		JDatabase
	 */
	function __construct( $options )
	{	
		$host		= (isset($options['host']))	? $options['host']		: 'localhost';
		$user		= (isset($options['user']))	? $options['user']		: '';
		$password	= (isset($options['password']))	? $options['password']	: '';
		$database	= (isset($options['database'])) ? $options['database']	: '';

		// perform a number of fatality checks, then return gracefully
		if (!function_exists( 'pg_connect' )) {
			// Legacy error handling switch based on the JError::$legacy switch.
			// @deprecated  11.3
			if (JError::$legacy) {
				$this->errorNum = 1;
				$this->errorMsg = JText::_('JLIB_DATABASE_ERROR_ADAPTER_POSTGRESQL');  // -> 'The PostgreSQL adapter "pg" is not available.';
				return;
			}
			else
				throw new JDatabaseException(JText::_('JLIB_DATABASE_ERROR_ADAPTER_POSTGRESQL'));  // -> 'The PostgreSQL adapter "pg" is not available.';
		}

		// connect to the server
		if (!($this->connection = @pg_connect( "host=$host user=$user password=$password dbname=$database" ))) {
			// Legacy error handling switch based on the JError::$legacy switch.
			// @deprecated  11.3
			if (JError::$legacy) {
				$this->errorNum = 2;
				$this->errorMsg = JText::_('JLIB_DATABASE_ERROR_CONNECT_POSTGRESQL');  // -> 'The PostgreSQL adapter "pg" is not available.';
				return;
			}
			else
				throw new JDatabaseException(JText::_('JLIB_DATABASE_ERROR_CONNECT_POSTGRESQL'));  // -> 'The PostgreSQL adapter "pg" is not available.';
		}

		// finalize initialization
		parent::__construct($options);
	}

	/**
	 * Database object destructor
	 *
	 * @return void
	 * @since 11.1
	 */
	public function __destruct()
	{
		if (is_resource($this->connection)) {
			pg_close($this->connection);
		}
	}
	
	/**
	 * Method to escape a string for usage in an SQL statement.
	 *
	 * @param   string  $text   The string to be escaped.
	 * @param   bool    $extra  Optional parameter to provide extra escaping.
	 *
	 * @return  string  The escaped string.
	 *
	 * @since   11.1
	 */
	public function escape($text, $extra = false)
	{
		$result = pg_escape_string( $this->getConnection() , $text );

		if ($extra) {
			$result = addcslashes($result, '%_');
		}

		return $result;
	}
		
	/**
	 * Test to see if the PostgreSQL connector is available
	 *
	 * @return boolean  True on success, false otherwise.
	 */
	public function test()
	{
		return (function_exists( 'pg_connect' ));
	}

	/**
	 * Determines if the connection to the server is active.
	 *
	 * @return	boolean
	 * @since	11.1
	 */
	public function connected()
	{
		if(is_resource($this->connection)) {
			return pg_ping($this->connection);
		}
		return false;
	}
	
	
	/**
	 * Drops a table from the database.
	 *
	 * @param   string  $tableName  The name of the database table to drop.
	 * @param   bool    $ifExists   Optionally specify that the table must exist before it is dropped.
	 *
	 * @return  bool	true
	 * @since   11.1
	 */
	function dropTable($tableName, $ifExists = true)
	{
		$query = $this->getQuery(true);

		$this->setQuery(
			'DROP TABLE '.
			($ifExists ? 'IF EXISTS ' : '').
			$query->quoteName($tableName)
		);

		$this->query();

		return true;
	}
	
	/**
	 * Description
	 *
	 * @return int The number of affected rows in the previous operation
	 * @since 1.0.5
	 */
	public function getAffectedRows()
	{
		return pg_affected_rows( $this->connection );
	}
	
	/**
	 * Method to get the database collation in use by sampling a text field of a table in the database.
	 *
	 * @return  mixed  The collation in use by the database or boolean false if not supported.
	 *
	 * @since   11.1
	 */
	public function getCollation()
	{
		if ( $this->hasUTF() ) {
			$cur = $this->query( 'SHOW LC_COLLATE;' );
			$coll = $this->fetchArray( $cur );
			return $coll['lc_collate'];
		} else {
			return 'N/A (Not Able to Detect)';
		}
	}
	
	/**
	 * Gets an exporter class object.
	 *
	 * @return  JDatabaseExporterMySQL  An exporter object.
	 * 
	 * @todo	Not yet implemented
	 *
	 * @since   11.1
	 */
	/*public function getExporter()
	{
		// Make sure we have an exporter class for this driver.
		if (!class_exists('JDatabaseExporterMySQL')) {
			throw new JDatabaseException(JText::_('JLIB_DATABASE_ERROR_MISSING_EXPORTER'));
		}

		$o = new JDatabaseExporterMySQL;
		$o->setDbo($this);

		return $o;
	}*/

	/**
	 * Gets an importer class object.
	 *
	 * @return  JDatabaseImporterMySQL  An importer object.
	 * 
	 * @todo	Not yet implemented
	 *
	 * @since   11.1
	 */
	/*public function getImporter()
	{
		// Make sure we have an importer class for this driver.
		if (!class_exists('JDatabaseImporterMySQL')) {
			throw new JDatabaseException(JText::_('JLIB_DATABASE_ERROR_MISSING_IMPORTER'));
		}

		$o = new JDatabaseImporterMySQL;
		$o->setDbo($this);

		return $o;
	}*/
	
	/**
	 * Get the number of returned rows for the previous executed SQL statement.
	 *
	 * @param   resource  $cursor  An optional database cursor resource to extract the row count from.
	 *
	 * @return  integer   The number of returned rows.
	 *
	 * @since   11.1
	 */
	public function getNumRows( $cur = null )
	{
		return pg_num_rows( $cur ? $cur : $this->cursor );
	}
	
	/**
	 * Get the current or query, or new JDatabaseQuery object.
	 *
	 * @param   bool   $new  False to return the last query set, True to return a new JDatabaseQuery object.
	 *
	 * @return  mixed  The current value of the internal SQL variable or a new JDatabaseQuery object.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	function getQuery($new = false)
	{
		if ($new) {
			// Make sure we have a query class for this driver.
			if (!class_exists('JDatabaseQueryPostgreSQL')) {
				throw new JDatabaseException(JText::_('JLIB_DATABASE_ERROR_MISSING_QUERY'));
			}
			return new JDatabaseQueryPostgreSQL($this);
		}
		else {
			return $this->sql;
		}
	}
	
	
	/**
	 * Shows the table CREATE statement that creates the given tables.
	 * 
	 * This is unsuported by PostgreSQL.
	 *
	 * @param   mixed  $tables  A table name or a list of table names.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */	
	public function getTableCreate($tables)
	{
		return '';
	}
	
	/**
	 * Get the details list of keys for a table.
	 *
	 * @param   string  $table  The name of the table.
	 *
	 * @return  array  An array of the column specification for the table.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function getTableKeys($table)
	{
		// To check if table exists and prevent SQL injection
		$tableList = $this->getTableList();
		
		if ( in_array($table, $tableList) )
		{
			// Get the details columns information.
			$query = $this->getQuery();
			$query->select('pgClass2nd.relname, pgIndex.*')
				  ->from ( 'pg_class AS pgClassFirst , pg_index AS pgIndex, pg_class AS pgClass2nd' )
				  ->where( 'pgClassFirst.oid=pgIndex.indrelid' )
				  ->where( 'pgClass2nd.relfilenode=pgIndex.indexrelid' )
				  ->where( 'pgClassFirst.relname=' . $this->quote($table) );
			$this->setQuery($query);
			$keys = $this->loadObjectList();
			
			return $keys;	
		}
		return false;
	}
	
	
	/**
	 * Method to get an array of all tables in the database.
	 *
	 * @return  array  An array of all the tables in the database.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function getTableList()
	{
		$query = $this->getQuery();
		$query->select('table_name')
			  ->from( 'information_schema.tables' )
			  ->where( 'table_type=' .  $this->quote('BASE TABLE') )
			  ->where( 'table_schema NOT IN (' . $this->quote('pg_catalog') . ', '
			  								   . $this->quote('information_schema') .' )' );
		$this->setQuery($query);
		$tables = $this->loadColumn();
		
		return $tables;
	}
	
	/**
	 * Get the version of the database connector.
	 *
	 * @return  string  The database connector version.
	 *
	 * @since   11.1
	 */
	public function getVersion()
	{
		$version = pg_version( $this->connection );
		return $version['server'];
	}
	
	/**
	 * Determines UTF support
	 *
	 * @return boolean True - UTF is supported
	 */
	public function hasUTF()
	{
		return true;
	}
	
	/**
	 * Method to get the auto-incremented value from the last INSERT statement.
	 * To be called after the INSERT statement, it's MANDATORY to have a sequence on 
	 * every primary key table.
	 *
	 * @return  integer  The value of the auto-increment field from the last inserted row.
	 * 
	 * @todo	could be implemented in three different modes
	 * 			1) lastval() after INSERT query (as implemented now)
	 * 			2) nextval('sequence') before INSERT query but need to know sequence name 
	 * 					and modify INSERT query element -> can be defined in a column 'id' 
	 * 			3) INSERT .. RETURNING .. (on postgresql>=8.2) but need to know the column 
	 * 					name autoincremented, make a fetch_row after insert query and modify 
	 * 					INSERT query element --> use RETURNING element, then loadRow/Assoc/Obj
	 * 
	 * @since   11.1
	 */
	public function insertid()
	{
		$this->setQuery('SELECT lastval();');
		$this->query();
		
		return (int) $this->fetchArray();
	}
	
	/**
	 * Execute the query
	 *
	 * @return mixed A database resource if successful, FALSE if not.
	 */
	public function query()
	{
		if (!is_resource($this->connection)) {
			// Legacy error handling switch based on the JError::$legacy switch.
			// @deprecated  11.3
			if (JError::$legacy) {

				if ($this->debug) {
					JError::raiseError(500, 'JDatabasePostgreSQL::query: '.$this->errorNum.' - '.$this->errorMsg);
				}
				return false;
			}
			else {
				JLog::add(JText::sprintf('JLIB_DATABASE_QUERY_FAILED', $this->errorNum, $this->errorMsg), JLog::ERROR, 'database');
				throw new JDatabaseException;
			}
		}

		// Take a local copy so that we don't modify the original query and cause issues later
		$sql = $this->replacePrefix((string) $this->sql);
		if ($this->limit > 0 || $this->offset > 0) {
			$sql .= ' LIMIT '.$this->limit.' OFFSET '.$this->offset;
		}
		
		// If debugging is enabled then let's log the query.
		if ($this->debug) {
			// Increment the query counter and add the query to the object queue.
			$this->count++;
			$this->log[] = $sql;

			JLog::add($sql, JLog::DEBUG, 'databasequery');
		}
		// Reset the error values.
		$this->errorNum = 0;
		$this->errorMsg = '';
		
		// Execute the query.
		$this->cursor = pg_query( $this->connection, $sql );

		if (!$this->cursor) {
			$this->errorNum = (int) pg_result_error_field( $this->cursor, PGSQL_DIAG_SQLSTATE ) . ' ';
			$this->errorMsg = (string) pg_result_error_field( $this->cursor, PGSQL_DIAG_MESSAGE_PRIMARY )." SQL=$sql <br />";
			
			// Legacy error handling switch based on the JError::$legacy switch.
			// @deprecated  11.3
			if (JError::$legacy) {

				if ($this->debug) {
					JError::raiseError(500, 'JDatabasePostgreSQL::query: '.$this->errorNum.' - '.$this->errorMsg );
				}
				return false;
			}
			else {
				JLog::add(JText::sprintf('JLIB_DATABASE_QUERY_FAILED', $this->errorNum, $this->errorMsg), JLog::ERROR, 'databasequery');
				throw new JDatabaseException;
			}
		}
		return $this->cursor;
	}
	
	
	/**
	 * Selects the database, but redundant for PostgreSQL
	 *
	 * @return bool Always true
	 */
	public function select($database=null) 
	{
		return true;
	}
	

	/**
	 * Custom settings for UTF support
	 */
	public function setUTF()
	{
		pg_set_client_encoding( $this->connection, 'UTF8' );
	}
	
	/**
	 * Method to fetch a row from the result set cursor as an array.
	 *
	 * @param   mixed  $cursor  The optional result set cursor from which to fetch the row.
	 *
	 * @return  mixed  Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   11.1
	 */
	protected function fetchArray($cursor = null)
	{
		return pg_fetch_row($cursor ? $cursor : $this->cursor);
	}

	/**
	 * Method to fetch a row from the result set cursor as an associative array.
	 *
	 * @param   mixed  $cursor  The optional result set cursor from which to fetch the row.
	 *
	 * @return  mixed  Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   11.1
	 */
	protected function fetchAssoc($cursor = null)
	{
		return pg_fetch_assoc($cursor ? $cursor : $this->cursor);
	}

	/**
	 * Method to fetch a row from the result set cursor as an object.
	 *
	 * @param   mixed   $cursor  The optional result set cursor from which to fetch the row.
	 * @param   string  $class   The class name to use for the returned row object.
	 *
	 * @return  mixed   Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   11.1
	 */
	protected function fetchObject($cursor = null, $class = 'stdClass')
	{
		return pg_fetch_object($cursor ? $cursor : $this->cursor, $class);
	}

	/**
	 * Method to free up the memory used for the result set.
	 *
	 * @param   mixed  $cursor  The optional result set cursor from which to fetch the row.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	protected function freeResult($cursor = null)
	{
		pg_free_result($cursor ? $cursor : $this->cursor);
	}
	
	/**
	 * Diagnostic method to return explain information for a query.
	 *
	 * @return      string  The explain output.
	 *
	 * @since       11.1
	 * @deprecated  11.2
	 */
	public function explain()
	{
		// Deprecation warning.
		JLog::add('JDatabase::explain() is deprecated.', JLog::WARNING, 'deprecated');
		
		$temp = $this->sql;
		$this->sql = "EXPLAIN $this->sql";

		if (!($cur = $this->query())) {
			return null;
		}
		$first = true;

		$buffer = '<table id="explain-sql">';
		$buffer .= '<thead><tr><td colspan="99">'.$this->getQuery().'</td></tr>';
		while ($row = $this->fetchAssoc($cursor)) {
			if ($first) {
				$buffer .= '<tr>';
				foreach ($row as $k=>$v) {
					$buffer .= '<th>'.$k.'</th>';
				}
				$buffer .= '</tr>';
				$first = false;
			}
			$buffer .= '</thead><tbody><tr>';
			foreach ($row as $k=>$v) {
				$buffer .= '<td>'.$v.'</td>';
			}
			$buffer .= '</tr>';
		}
		$buffer .= '</tbody></table>';
		
		// Restore the original query to it's state before we ran the explain.
		$this->sql = $temp;
		
		// Free up system resources and return.
		$this->freeResult($cursor);

		return $buffer;
	}

	/**
	 * Retrieves field information about a given table.
	 *
	 * @param   string  $table     The name of the database table.
	 * @param   bool    $typeOnly  True to only return field types.
	 *
	 * @return  array  An array of fields for the database table.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function getTableColumns( $tables, $typeonly = true )
	{
		settype($tables, 'array'); //force to array
		$result = array();
		
		// To check if table exists and prevent SQL injection
		$tableList = $this->getTableList();
		
		foreach ($tables as $tblval) 
		{
			if ( in_array($tblval, $tableList) )
			{
				$query = $this->getQuery();
				$query->select('column_name,data_type')
					  ->from( 'information_schema.columns' )
					  ->where( 'table_name=' . $this->quote($tblval) );
				$this->setQuery($query);
				
				$fields = $this->loadObjectList();
	
				if ($typeonly) {
					foreach ($fields as $field) {
						$result[$tblval][$field->column_name] = preg_replace("/[(0-9)]/",'', $field->data_type );
					}
				} else {
					foreach ($fields as $field) {
						$result[$tblval][$field->column_name] = $field;
					}
				}
			}
		}

		return $result;
	}
	
	
	
	/* EXTRA FUNCTION postgreSQL */
	
	/**
	 * Get the substring position inside a string
	 *
	 * @param string The string being sought
	 * @param string The string/column being searched
	 * @return int   The position of $substring in $string
	 */
	public function getStringPositionSQL( $substring, $string )
	{
		$query = "SELECT POSITION( $substring IN $string )" ;
		$this->setQuery( $query );
		$position = $this->loadRow();
		
		return $position['position'];
	}

	/**
	 * Generate a random value
	 *
	 * @return float The random generated number
	 */
	public function getRandom()
	{		
		$this->setQuery( 'SELECT RANDOM()' );
		$random = $this->loadRow();
		
		return $random['random'];
	}

	/**
	 * Create the database and associate it to the user.
	 * IMPORTANT: the user role MUST be created before using this function.
	 *
	 * @param	string	The database name
	 * @param	bool	Whether or not to create with UTF support (only here for function signature compatibility)
	 * @return	bool	True if all was ok
	 * 
	 * @since	11.1
	 * @throws  JDatabaseException
	 */
	public function createDatabase( $options, $DButfSupport )
	{
		if ( !(isset($options['user'])) || ! (isset($options['database'])) )
			throw new JDatabaseException(JText::_('JLIB_DATABASE_ERROR_POSTGRESQL_CANT_CREATE_DB'));  // -> Can't create DB, no needed info
		
		$sql = 'CREATE DATABASE '.$this->quoteName( $options['database'] ) . ' OWNER ' . $this->quoteName($options['user']) ;

		if ( $DButfSupport )
			$sql .= ' ENCODING UTF8' ;
		
		$this->setQuery($sql);
		$this->query();
		
		return true;
	}

	/**
	 * Rename a database table
	 *
	 * @param	string	The old table name
	 * @param	string	The new table name
	 * @return	bool	True if all was ok
	 * 
	 * @throws	JDatabaseException
	 */
	public function renameTable($oldTable, $newTable)
	{
		// To check if table exists and prevent SQL injection
		$tableList = $this->getTableList();
		
		// Origin Table does not exist
		if ( !in_array($oldTable, $tableList) )
		{
			throw new JDatabaseException(JText::_('JLIB_DATABASE_ERROR_POSTGRESQL_TABLE_NOT_FOUND'));  // -> Origin Table not found	
		}
		else 
		{
			$this->setQuery('ALTER TABLE ' . $this->escape($oldTable) . ' RENAME TO ' . $this->escape($newTable) );
			$this->query();
		}

		return true;
	}

	
	/**
	 * Sets the SQL statement string for later execution of a transaction block.
	 *
	 * @param   mixed    $query   The SQL statement to set either as a JDatabaseQuery object or a string.
	 * @param   integer  $limit   The maximum affected rows to set.
	 * @param   integer  $offset  The affected row offset to set.
	 *
	 * @return  JDatabase  This object to support method chaining.
	 *
	 * @since   11.1
	 */
	public function setTransactionQuery($query, $limit = 0, $offset = 0)
	{
		$query->limit($limit, $offset);		// to not break compatibility
		array_push($this->sql, $query);     // ordered query list for transactions
		
		// limit query element
		//$this->limit				= (int) $limit;
		//$this->offset				= (int) $offset;

		return $this;
	}
	
	/**
	 * Execute a transaction query
	 *
	 * @return	bool	Return true if ok
	 * 
	 * @since	11.1
	 * 
	 * @throws  JDatabaseException
	 */
	public function transactionQuery()
	{
		if (!is_resource($this->connection)) {
			JLog::add(JText::sprintf('JLIB_DATABASE_QUERY_FAILED', $this->errorNum, $this->errorMsg), JLog::ERROR, 'database');
			throw new JDatabaseException;
		}
		
		while( list( , $query ) = each ( $this->sql ) )
		{
			// Take a local copy so that we don't modify the original query and cause issues later
			//$sql = $this->replacePrefix((string) $this->sql);
			$sql = $this->replacePrefix((string) $query);
			
			// If debugging is enabled then let's log the query.
			if ($this->debug) {
				// Increment the query counter and add the query to the object queue.
				$this->count++;
				$this->log[] = $sql;
	
				JLog::add($sql, JLog::DEBUG, 'databasequery');
			}
			// Reset the error values.
			$this->errorNum = 0;
			$this->errorMsg = '';
			
			// Execute the query.
			$this->cursor = pg_query( $this->connection, $sql );
	
			if (!$this->cursor) {
				$this->errorNum = (int) pg_result_error_field( $this->cursor, PGSQL_DIAG_SQLSTATE ) . ' ';
				$this->errorMsg = (string) pg_result_error_field( $this->cursor, PGSQL_DIAG_MESSAGE_PRIMARY )." SQL=$sql <br />";
				
				JLog::add(JText::sprintf('JLIB_DATABASE_QUERY_FAILED', $this->errorNum, $this->errorMsg), JLog::ERROR, 'databasequery');
				throw new JDatabaseException;
			}
		}
		
		return true; //$this->cursor;
	}

}
