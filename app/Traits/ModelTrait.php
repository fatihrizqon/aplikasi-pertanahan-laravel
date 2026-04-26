<?php

namespace App\Traits;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Laravel\Scout\Searchable;

trait ModelTrait
{
    use Searchable;

	public function getFields()
	{
		static $columns;
        $columns ??= Schema::getColumnListing($this->getTable());
		return $columns;
	}

	public function hasField($field)
	{
        return Schema::hasTable($this->getTable(), $field);
	}

	public function autoFill(array $params, array $customFields = [])
	{
		foreach ($customFields as $field) {
			if (array_key_exists($field, $params)) {
				$this->$field = $params[$field];
			}
		}

		return $this->fill(Arr::only($params, $this->getFields()));
	}

	public static function options($key, $val, $query = null)
	{
		$query ??= static::query();
		$models = $query->get();

		$options = [];
		foreach ($models as $model) {
			$options[$model->$key] = $model->$val;
		}
		return $options;
	}


    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (!$search) {
            return $query;
        }

        $ids = static::search($search)
                     ->take(1000)
                     ->keys();

        return $query->whereIn('id', $ids);
    }

    public function scopeFilter(Builder $query, $filters)
    {
        if (!$filters) {
            return $query;
        }

        foreach ($filters as $column => $value) {
            if ($value === '' || $value === null) {
                continue;
            }

            if ($value === 'true') {
                $query->whereNotNull($column);
            }
            elseif ($value === 'false') {
                $query->whereNull($column);
            }
            else {
                $query->where($column, $value);
            }
        }

        return $query;
    }
}
