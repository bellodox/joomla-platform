<?php
/**
 * @version    $Id: JDatabaseImporterPostgresqlTestre91.php gpongelli $
 * @package    Joomla.UnitTest
 * 
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters. All rights reserved.
 * @license    GNU General Public License
 */

require_once __DIR__ . '/JDatabaseImporterPostgresqlInspector.php';
require_once __DIR__ . '/JDatabaseImporterPostgresqlTest.php';

/**
 * Test the JDatabaseImporterPostgresql class.
 * 
 * @package     Joomla.UnitTest
 * @subpackage  Database
 * 
 * @since       12.1
 */
class JDatabaseImporterPostgresqlTestPre91 extends JDatabaseImporterPostgresqlTest
{
	/**
	 * Sets up the testing conditions
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function setup()
	{
		// Set up the database object mock.

		$this->dbo = $this->getMock(
			'JDatabasePostgresql',
			array(
				'getErrorNum',
				'getPrefix',
				'getTableColumns',
				'getTableKeys',
				'getTableSequences',
				'getAddSequenceSQL',
				'getChangeSequenceSQL',
				'getDropSequenceSQL',
				'getAddIndexSQL',
				'getVersion',
				'quoteName',
				'loadObjectList',
				'quote',
				'setQuery',
			),
			array(),
			'',
			false
		);

		$this->dbo->expects(
			$this->any()
		)
		->method('getPrefix')
		->will(
			$this->returnValue(
				'jos_'
			)
		);

		$this->dbo->expects(
			$this->any()
		)
		->method('getTableColumns')
		->will(
			$this->returnValue(
				array(
					'id' => (object) array(
						'Field' => 'id',
						'Type' => 'integer',
						'Null' => 'NO',
						'Default' => 'nextval(\'jos_dbtest_id_seq\'::regclass)',
						'Comments' => '',
					),
					'title' => (object) array(
						'Field' => 'title',
						'Type' => 'character varying(50)',
						'Null' => 'NO',
						'Default' => 'NULL',
						'Comments' => '',
					),
				)
			)
		);

		$this->dbo->expects(
			$this->any()
		)
		->method('getTableKeys')
		->will(
			$this->returnValue(
				array(
					(object) array(
						'Index' => 'jos_dbtest_pkey',
						'is_primary' => 'TRUE',
						'is_unique' => 'TRUE',
						'Query' => 'ALTER TABLE jos_dbtest ADD PRIMARY KEY (id)',
					),
					(object) array(
						'Index' => 'jos_dbtest_idx_name',
						'is_primary' => 'FALSE',
						'is_unique' => 'FALSE',
						'Query' => 'CREATE INDEX jos_dbtest_idx_name ON jos_dbtest USING btree (name)',
					)
				)
			)
		);

		/* Check if database is at least 9.1.0 */
		$this->dbo->expects(
			$this->any()
		)
		->method('getVersion')
		->will(
			$this->returnValue(
				'8.4.0'
			)
		);

		if (version_compare($this->dbo->getVersion(), '9.1.0') >= 0)
		{
			$start_val = '1';
		}
		else
		{
			/* Older version */
			$start_val = null;
		}

		$this->dbo->expects(
			$this->any()
		)
		->method('getTableSequences')
		->will(
			$this->returnValue(
			array(
					(object) array(
						'Name' => 'jos_dbtest_id_seq',
						'Schema' => 'public',
						'Table' => 'jos_dbtest',
						'Column' => 'id',
						'Type' => 'bigint',
						'Start_Value' => $start_val,
						'Min_Value' => '1',
						'Max_Value' => '9223372036854775807',
						'Increment' => '1',
						'Cycle_option' => 'NO',
					)
				)
			)
		);

		$this->dbo->expects(
			$this->any()
		)
		->method('quoteName')
		->will(
			$this->returnCallback(
				array($this, 'callbackQuoteName')
			)
		);

		$this->dbo->expects(
			$this->any()
		)
		->method('quote')
		->will(
			$this->returnCallback(
				array($this, 'callbackQuote')
			)
		);

		$this->dbo->expects(
			$this->any()
		)
		->method('setQuery')
		->will(
			$this->returnCallback(
				array($this, 'callbackSetQuery')
			)
		);

		$this->dbo->expects(
			$this->any()
		)
		->method('loadObjectList')
		->will(
			$this->returnCallback(
				array($this, 'callbackLoadObjectList')
			)
		);
	}

	/**
	 * Tests the asXml method.
	 *
	 * @return void
	 *
	 * @since  12.1
	 */
	public function testAsXml()
	{
		parent::testAsXml();
	}

	/**
	 * Tests the check method.
	 *
	 * @return void
	 *
	 * @since  12.1
	 */
	public function testCheckWithNoDbo()
	{
		parent::testCheckWithNoDbo();
	}

	/**
	 * Tests the check method.
	 *
	 * @return void
	 *
	 * @since  12.1
	 */
	public function testCheckWithNoFrom()
	{
		parent::testCheckWithNoFrom();
	}

	/**
	 * Tests the check method.
	 *
	 * @return void
	 *
	 * @since  12.1
	 */
	public function testCheckWithGoodInput()
	{
		parent::testCheckWithGoodInput();
	}

	/**
	 * Tests the from method with expected good inputs.
	 *
	 * @return void
	 *
	 * @since  12.1
	 */
	public function testFromWithGoodInput()
	{
		parent::testFromWithGoodInput();
	}

	/**
	 * Tests the getAddColumnSQL method.
	 *
	 * Note that combinations of fields is tested in testGetColumnSQL.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetAddColumnSQL()
	{
		parent::testGetAddColumnSQL();
	}

	/**
	 * Tests the getAddSequenceSQL method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetAddSequenceSQL()
	{
		parent::testGetAddSequenceSQL();
	}

	/**
	 * Tests the getAddIndexSQL method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetAddIndexSQL()
	{
		parent::testGetAddIndexSQL();
	}

	/**
	 * Tests the getAlterTableSQL method.
	 *
	 * @param   SimpleXMLElement  $structure  XML structure of field
	 * @param   string            $expected   Expected string
	 * @param   string            $message    Error message
	 *
	 * @return  void
	 *
	 * @since   12.1
	 *
	 * @dataProvider dataGetAlterTableSQL
	 */
	public function testGetAlterTableSQL($structure, $expected, $message)
	{
		parent::testGetAlterTableSQL($structure, $expected, $message);
	}

	/**
	 * Tests the getChangeColumnSQL method.
	 *
	 * Note that combinations of fields is tested in testGetColumnSQL.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetChangeColumnSQL()
	{
		parent::testGetChangeColumnSQL();
	}

	/**
	 * Tests the getChangeSequenceSQL method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetChangeSequenceSQL()
	{
		parent::testGetChangeSequenceSQL();
	}

	/**
	 * Tests the getColumnSQL method.
	 *
	 * @param   SimpleXmlElement  $field     The database field as an object.
	 * @param   string            $expected  The expected result from the getColumnSQL method.
	 * @param   string            $message   The error message to display if the result does not match the expected value.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 *
	 * @dataProvider dataGetColumnSQL
	 */
	public function testGetColumnSQL($field, $expected, $message)
	{
		parent::testGetColumnSQL($field, $expected, $message);
	}

	/**
	 * Tests the getDropColumnSQL method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetDropColumnSQL()
	{
		parent::testGetDropColumnSQL();
	}

	/**
	 * Tests the getDropKeySQL method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetDropIndexSQL()
	{
		parent::testGetDropIndexSQL();
	}

	/**
	 * Tests the getDropPrimaryKeySQL method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetDropPrimaryKeySQL()
	{
		parent::testGetDropPrimaryKeySQL();
	}

	/**
	 * Tests the getDropSequenceSQL method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetDropSequenceSQL()
	{
		parent::testGetDropSequenceSQL();
	}

	/**
	 * Tests the getIdxLookup method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetIdxLookup()
	{
		parent::testGetIdxLookup();
	}

	/**
	 * Tests the getRealTableName method with the wrong type of class.
	 *
	 * @return void
	 *
	 * @since  12.1
	 */
	public function testGetRealTableName()
	{
		parent::testGetRealTableName();
	}

	/**
	 * Tests the setDbo method with the wrong type of class.
	 *
	 * @return void
	 *
	 * @since  12.1
	 */
	public function testSetDboWithBadInput()
	{
		parent::testSetDboWithBadInput();
	}

	/**
	 * Tests the setDbo method with the wrong type of class.
	 *
	 * @return void
	 *
	 * @since  12.1
	 */
	public function testSetDboWithGoodInput()
	{
		parent::testSetDboWithGoodInput();
	}

	/**
	 * Tests the withStructure method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testWithStructure()
	{
		parent::testWithStructure();
	}
}
