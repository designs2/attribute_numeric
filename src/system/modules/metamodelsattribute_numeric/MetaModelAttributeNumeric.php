<?php

/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 * @package    MetaModels
 * @subpackage AttributeNumeric
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

/**
 * This is the MetaModelAttribute class for handling numeric fields.
 *
 * @package    MetaModels
 * @subpackage AttributeNumeric
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 */
class MetaModelAttributeNumeric extends MetaModelAttributeSimple
{

	public function getSQLDataType()
	{
		// TODO: is the default value here really a wise idea?
		return 'int(10) NULL default NULL';
	}

	public function getAttributeSettingNames()
	{
		return array_merge(parent::getAttributeSettingNames(), array(
			'filterable',
			'searchable',
			'sortable',
			'flag'
		));
	}

	public function getFieldDefinition($arrOverrides = array())
	{
		$arrFieldDef=parent::getFieldDefinition($arrOverrides);
		$arrFieldDef['inputType'] = 'text';
		$arrFieldDef['eval']['rgxp'] = 'digit';
		return $arrFieldDef;
	}

	////////////////////////////////////////////////////////////////////////////
	// IMetaModelAttribute functions
	////////////////////////////////////////////////////////////////////////////

	/**
	 * Filter all values greater than the passed value.
	 *
	 * @param mixed $varValue     The value to use as lower end
	 *
	 * @param bool  $blnInclusive If true, the passed value will be included, if false, it will be excluded.
	 *
	 * @return int[] The list of item ids of all items matching the condition.
	 */
	public function filterGreaterThan($varValue, $blnInclusive = false)
	{
		return $this->getIdsFiltered($varValue, ($blnInclusive) ? '>=' : '>');
	}

	/**
	 * Filter all values less than the passed value.
	 *
	 * @param mixed $varValue     The value to use as upper end.
	 *
	 * @param bool  $blnInclusive If true, the passed value will be included, if false, it will be excluded.
	 *
	 * @return int[] The list of item ids of all items matching the condition.
	 */
	public function filterLessThan($varValue, $blnInclusive = false)
	{
		return $this->getIdsFiltered($varValue, ($blnInclusive) ? '<=' : '<');
	}

	/**
	 * Filter all values not having the passed value.
	 *
	 * @param mixed $varValue     The value to use as upper end.
	 *
	 * @return array The list of item ids of all items matching the condition.
	 */
	public function filterNotEqual($varValue)
	{
		return $this->getIdsFiltered($varValue, '!=');
	}

	/**
	 * Filter all values by specified operation.
	 *
	 * @param int    $varValue     The value to use as upper end.
	 *
	 * @param string $strOperation The specified operation like greater than, lower than etc.
	 *
	 * @return int[] The list of item ids of all items matching the condition.
	 */
	protected function getIdsFiltered($varValue, $strOperation)
	{
		$strSql = sprintf('SELECT id FROM %s WHERE %s %s %d',
				$this->getMetaModel()->getTableName(),
				$this->getColName(),
				$strOperation,
				intval($varValue)
				);

		$objIds = Database::getInstance()->executeUncached($strSql);
		return $objIds->fetchEach('id');
	}

}
