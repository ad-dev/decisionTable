<?php
namespace App\Contracts;

interface DecisionTableInterface
{
	/**
	 * Sets data for decision table
	 *
	 * @param array $data
	 */
	function setData(array $data);
	/**
	 * Adds condition to decision table
	 *
	 * @param string $name
	 * @param array $fieldsToCompare
	 */
	function addCondition($name, array $fieldsToCompare);
	/**
	 * Adds rule to decision table
	 *
	 * @param string $name
	 * @param array $conditions
	 */
	function addRule($name, array $conditions);
	/**
	 * Adds action to decision table
	 *
	 * @param string $name
	 * @param array $rulesY
	 * @param array $rulesN
	 */
	function addAction($name, array $rulesY, array $rulesN);
	
	/**
	 * Checks if rules validate and
	 * $action can be taken ($status = true)
	 * or $action cannot be taken ($status = false)
	 * @param string $action
	 * @param bool $status
	 */
	function check($action, $status);
}