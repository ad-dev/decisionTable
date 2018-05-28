<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Contracts\DecisionTableInterface;

class ListClaimableFlightsCommand extends Command
{

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'flights:list-claimable
							{csv_filename : a CSV file containing flights data}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'List all flights given in CSV file and show claimable status for each flight';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	private function buildDecisionTable(): DecisionTableInterface
	{
		$dt = app()->make('App\Contracts\DecisionTable');
		
		// getting EU countries list and stores them in cache
		$countries = app()->make('CountriesFeed');
		$countriesList = $countries->fetchEUCountries();
		
		// adding conditions
		
		/*
		 * condition entry format:
		 *
		 * [operandA, operator, operandB]
		 *
		 * OperandA - name of data field to check
		 * (data [one row] will be loaded from CSV file
		 * using DecisionTableInterface::setData() method)
		 * Operator - comparison operator
		 * OperandB - compare operandA to this value
		 * 
		 * Possible operators:
		 * in - checks if operandA is in operandB
		 * le - operandA <= operandB
		 * ge - operandA >= operandB
		 * eq - operandA == operandB
		 */
		
		$dt->addCondition('Departure_from_EU', [
			[
				'country', // OperandA
				'in', // Operator: comparison operator
				array_keys($countriesList) // OperandB
			]
		]);
		
		$dt->addCondition('Cancelled_le_14d', [
			[
				'status_detail',
				'le',
				14
			],
			[
				'status',
				'eq',
				'Cancel'
			]
		
		]);
		
		$dt->addCondition('Departure_delay_ge_3h', [
			[
				'status_detail',
				'ge',
				3
			],
			[
				'status',
				'eq',
				'Delay'
			]
		]);
		
		// constructing rules
		
		$dt->addRule('Rule1', [
			'Departure_from_EU' => true, // rule name => expected status
			                             // (rule should pass or fail)
			'Cancelled_le_14d' => true
		]);
		
		$dt->addRule('Rule2', [
			'Departure_from_EU' => true,
			'Departure_delay_ge_3h' => true
		]);
		
		$dt->addRule('Rule3', [
			'Departure_from_EU' => true,
			'Cancelled_le_14d' => false
		]);
		
		$dt->addRule('Rule4', [
			'Departure_from_EU' => true,
			'Departure_delay_ge_3h' => false
		]);
		
		$dt->addRule('Rule5', [
			'Departure_from_EU' => false
		]);
		
		// adding "claimable" action
		
		$dt->addAction('claimable', 
			// flight is claimable if rules below passes
			[
				'Rule1',
				'Rule2'
			],
			// flight is not claimable if rules below passes
			[
				'Rule3',
				'Rule4',
				'Rule5'
			]);
		
		return $dt;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$dt = $this->buildDecisionTable(); // builds decision table

		try {
			$fh = fopen($this->argument('csv_filename'), 'r');
		} catch (\Exception $e) {
			$this->error('Cannot open CSV file. ' . $e->getMessage());
			return false;
		}

		while (! feof($fh)) {
			$row = fgetcsv($fh);
			if (is_array($row) && count($row) >= 3) {

				// Sets data
				$dt->setData([
					'country' => $row[0],
					'status' => $row[1],
					'status_detail' => $row[2]
				]);

				// check if flight is claimable (using data set above)
				$is_claimable = $dt->check('claimable', true);
				$row[] = $is_claimable ? 'Y' : 'N';
				$this->info(implode(' ', $row));
			} else {
				$this->warn('Incorrect data: ' . json_encode($row));
			}
		}
		fclose($fh);
	}
}
