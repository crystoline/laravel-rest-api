<?php

namespace Crystoline\LaraRestApi;

use Illuminate\Database\Query\Builder;

trait ApiModelTrait {

    /**
     * @param $query
     * @param $field
     * @param $value
     */
    public function scopeWhereOrNull(Builder $query, string $field, $value){
        $value = strtolower($value);
        $query->whereRaw("LOWER({$field}) = '{$value}'")
            ->orWhereNull($field);
    }
	/**
	 * Define a unique field for validation rule
	 * @var null
	 */
	static $uniqueField = null;

    /**
     * Return array of searchable fields
     * @return array
     */
    public static function searchable(): array {
		return [];
	}

    /**
     * @return array
     */
    public static function orderBy(): array {
        return [
        ];
    }
	/**
	 * @return array
	 */
	public static function getValidationRules(): array {
		return [];
	}

	/**
	 * @return array
	 */
	public static function getValidationMessages(): array {
		return [];
	}

	/**
	 * @return array
	 */
	public function getValidationRulesForUpdate(){
		$id = $this->id;
		$rules = self::getValidationRules();
		$fields = $this->getUniqueFields();
		foreach ($fields as $field){
			if(isset($rules[$field])){
				$rules[$field] .= ','.$id;
			}
		}
		return $rules;
	}

	/**
	 * get fields to be update
	 * @return array
	 */
	public function fieldsToUpdate(){
		return [];
	}

    /**
     * Return array of unique field. Used for validation
     * @return array
     */
    public static function getUniqueFields(): array {
		return [];
	}

}