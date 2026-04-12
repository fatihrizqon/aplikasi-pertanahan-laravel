<?php

namespace App\Traits;

use Illuminate\Validation\Validator;

trait ValidatableTrait
{
	/**
	 * @return array
	 */
	public function labels()
	{
		return [];
	}

	/**
	 * @return array
	 */
	public function errors()
	{
		return [];
	}

	/**
	 * @return array
	 */
	public function rules($scenario = null)
	{
		$scenarios = [
			null => [],
			'create' => [],
			'update' => [],
		];

		$rules = array_merge_recursive($scenarios[null], $scenarios[$scenario] ?? []);
		return $rules;
	}

	/**
	 * @return Validator
	 */
    public function validator(array $params, ?array $rules = null)
    {
        $rules = $rules ?? $this->rules();
        $errors = $this->errors();
        $labels = $this->labels();

        return validator($params, $rules, $errors, $labels);
    }
}
