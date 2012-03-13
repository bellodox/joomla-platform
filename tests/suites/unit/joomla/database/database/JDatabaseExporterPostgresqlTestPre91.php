<?php
/**
 * @version    $Id: JDatabaseExporterPostgresqlTestre91.php gpongelli $
 * @package    Joomla.UnitTest
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters. All rights reserved.
 * @license    GNU General Public License
 */

require_once __DIR__ . '/JDatabaseExporterPostgresqlInspector.php';
require_once __DIR__ . '/JDatabaseExporterPostgresqlTest.php';

/**
 * Test the JDatabaseExporterPostgresql class.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Database
 *
 * @since       12.1
 */
class JDatabaseExporterPostgresqlTestPre91 extends JDatabaseExporterPostgresqlTest
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
				'getVersion',
				'quoteName',
				'loadObjectList',
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
					(object) array(
						'column_name' => 'id',
						'type' => 'integer',
						'null' => 'NO',
						'default' => 'nextval(\'jos_dbtest_id_seq\'::regclass)',
						'comments' => '',
					),
					(object) array(
						'column_name' => 'title',
						'type' => 'character varying(50)',
						'null' => 'NO',
						'default' => 'NULL',
						'comments' => '',
					),
					(object) array(
						'column_name' => 'start_date',
						'type' => 'timestamp without time zone',
						'null' => 'NO',
						'default' => 'NULL',
						'comments' => '',
					),
					(object) array(
						'column_name' => 'description',
						'type' => 'text',
						'null' => 'NO',
						'default' => 'NULL',
						'comments' => '',
					)
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
						'idxName' => 'jos_dbtest_pkey',
						'isPrimary' => 'TRUE',
						'isUnique' => 'TRUE',
						'Query' => 'ALTER TABLE "jos_dbtest" ADD PRIMARY KEY (id)',
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
			$this->_ver9dot1 = true;
			$start_val = '1';
		}
		else
		{
			/* Older version */
			$this->_ver9dot1 = false;
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
						'sequence' => 'jos_dbtest_id_seq',
						'schema' => 'public',
						'table' => 'jos_dbtest',
						'column' => 'id',
						'data_type' => 'bigint',
						'start_value' => $start_val,
						'minimum_value' => '1',
						'maximum_value' => '9223372036854775807',
						'increment' => '1',
						'cycle_option' => 'NO',
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
	 * Test the magic __toString method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__toString()
	{
		parent::test__toString();
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
	 * Test the buildXML method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testBuildXml()
	{
		parent::testBuildXml();
	}

	/**
	 * Tests the buildXmlStructure method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testBuildXmlStructure()
	{
		parent::testBuildXmlStructure();
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
	public function testCheckWithNoTables()
	{
		parent::testCheckWithNoTables();
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
	 * Tests the from method with bad input.
	 *
	 * @return void
	 *
	 * @since  12.1
	 */
	public function testFromWithBadInput()
	{
		parent::testFromWithBadInput();
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
	 * Tests the method getGenericTableName method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetGenericTableName()
	{
		parent::testGetGenericTableName();
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
