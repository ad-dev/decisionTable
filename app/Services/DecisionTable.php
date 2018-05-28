<?php
namespace App\Services;

use App\Contracts\DecisionTableInterface;

class DecisionTable implements DecisionTableInterface
{

	private $data = [];

	private $conditions = [];

	private $rules = [];

	private $actions = [];

	public function setData(array $data)
	{
		$this->data = $data;
	}

	public function addRule($name, array $conditions)
	{
		$this->rules[$name] = $conditions;
	}

	public function addCondition($name, array $fieldsToCompare)
	{
		$this->conditions[$name] = $fieldsToCompare;
	}

	public function addAction($name, array $rulesY, array $rulesN)
	{
		$this->actions[$name][0] = $rulesN;
		$this->actions[$name][1] = $rulesY;
	}

	public function check($actionName, $status)
	{
		$rulesStatus = false;
		
		if (!isset($this->actions[$actionName][(int) $status])) {
			throw new \Exception('Invalid action status given. It can be true or false');
		}
		$actionRules = $this->actions[$actionName][(int) $status];

		// Rule1, Rule2....
		foreach ($actionRules as $rule) {
			$currentRuleStatus = true;
			// Rule X:
			// condition1 => expected status, condition2 => expected status....
			foreach ($this->rules[$rule] as $conditionName => $expectedConditionStatus) {
				$finalConditionStatus = true;
				// Condition body: [ [operandA, operator, operandB], .... ]
				foreach ($this->conditions[$conditionName] as $fields) {
					$conditionPartStatus = false;
					if (empty($fields[0]) || ! is_string($fields[0])) {
						throw new \Exception('Invalid or missing operand A');
					}
					if (empty($fields[1])) {
						throw new \Exception('Missing operator');
					}
					if (empty($fields[2])) {
						throw new \Exception('Missing operand B');
					}
					$operandA = $fields[0];
					$operator = $fields[1];
					$operandB = $fields[2];
					
					// evaluates condition part
					// [operandA, operator, operandB] according to operator
					switch ($operator) {
						case 'in':
							if (! is_array($operandB)) {
								throw new \Exception($rule . '' . 'Invalid operand B. an array expected');
							}
							foreach ($operandB as $item) {
								if (isset($this->data[$operandA]) && $this->data[$operandA] == $item) {
									// (new Dumper())->dump($item);
									$conditionPartStatus = true;
									break;
								}
							}
							break;
						case 'eq':
							if (isset($this->data[$operandA]) && $this->data[$operandA] == $operandB) {
								$conditionPartStatus = true;
								break;
							}
							break;
						case 'ge':
							if (isset($this->data[$operandA]) && $this->data[$operandA] >= $operandB) {
								$conditionPartStatus = true;
								break;
							}
							break;
						case 'le':
							if (isset($this->data[$operandA]) && $this->data[$operandA] <= $operandB) {
								$conditionPartStatus = true;
								break;
							}
							break;
						default:
							throw new \Exception($rule . '' . 'Unknown operator');
							break;
					}
					
					$finalConditionStatus = $finalConditionStatus && $conditionPartStatus;
				}
				$currentRuleStatus = $currentRuleStatus && ($finalConditionStatus === $expectedConditionStatus);
			}
			$rulesStatus = $rulesStatus || $currentRuleStatus;
		}
		return $rulesStatus;
	}
}