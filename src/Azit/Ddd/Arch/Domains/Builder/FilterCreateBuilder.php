<?php

namespace Azit\Ddd\Arch\Domains\Builder;

use Azit\Ddd\Arch\Constant\ValueConstant;
use Azit\Ddd\Model\BaseBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class FilterCreateBuilder {
    protected Collection $queries;
    protected Collection $columns;
    protected Collection $selects;
    protected array $attributes;

    public const OPERATOR_AND = 'and';
    public const OPERATOR_OR = 'or';

    public const OPERATOR_ILIKE = 'ilike';

    /**
     * Constructor
     * @param array $attributes
     */
    public function __construct(array $attributes) {
        $this -> attributes = $attributes;
        $this -> columns = collect();
        $this -> selects = collect();
        $this -> queries = collect([
            BaseBuilder::QUERY_INLINE => [],
            BaseBuilder::QUERY_NESTED => [],
        ]);
    }

    /**
     * Metodo que mezcla los tipos de condicionales
     * @param int $typeQuery
     * @param string $table
     * @param bool $isRelation
     * @param string $logic
     * @return void
     */
    private function query(int $typeQuery, string $table, bool $isRelation, string $logic){
        if ($this -> columns -> count() > ValueConstant::ARRAY_SIZE_EMPTY) {
            $cached = $this -> queries -> pull($typeQuery);

            $custom = [
                [
                    'table' => $table,
                    'relation' => $isRelation,
                    'boolean' => $logic,
                    'columns' => $this -> columns -> toArray()
                ]
            ];

            if ($this -> selects -> count() > ValueConstant::ARRAY_SIZE_EMPTY) {
                $custom[ValueConstant::ARRAY_SIZE_EMPTY]['selects'] = $this -> selects -> toArray();
            }

            $merged = collect($cached) -> merge($custom);
            $this -> queries -> put($typeQuery, $merged);
        }

        $this -> columns = collect();
        $this -> selects = collect();
    }

    /**
     * Condicional lineal
     * @param int $typeQuery
     * @param string $logic
     * @return void
     */
    public function addQuery(int $typeQuery, string $logic){
        $this -> query($typeQuery, BaseBuilder::RELATION_HOST, false, $logic);
    }

    /**
     * Condicionales anidas
     * @param int $typeQuery
     * @param string $table
     * @param bool $isRelation
     * @param string $logic
     * @return void
     */
    public function addQueryNested(int $typeQuery, string $table, bool $isRelation, string $logic){
        $this -> query($typeQuery, $table, $isRelation, $logic);
    }

    /**
     * Agregar columnas que permiten condicionales
     * @param string $column
     * @param string|null $keyValue
     * @param string $operator
     * @param string $logic
     * @param mixed|null $defaultValue Agregar un valor si la key no existe
     * @return void
     */
    public function addColumn(string $column, string $keyValue = null, string $operator = '=', string $logic = self::OPERATOR_AND, mixed $defaultValue = null) {
        $isKeyExists =  Arr::exists($this -> attributes, $keyValue);

        if (!$isKeyExists && !isset($defaultValue)){
            return;
        }

        $value = $isKeyExists ? Arr::get($this -> attributes, $keyValue) : $defaultValue;

        if (Str::contains($operator, 'like')){
            $this -> columns -> add([$column, $operator, Str::replace('?', $value,'%?%'), $logic]);
        }

        if (!Str::contains($operator, 'like')){
            $this -> columns -> add([$column, $operator, $value, $logic]);
        }
    }

    /**
     * Agregar columnas a la relacion
     * @param array $selects
     * @return void
     */
    public function addSelects(array $selects){
        $this -> selects = collect($selects);
    }

    /**
     * Regresa el array de las condicionales anidadas o lineales
     * @return array
     */
    public function toArray() : array {
        if ($this -> queries -> get(BaseBuilder::QUERY_INLINE) == null){
            $this -> queries -> forget(BaseBuilder::QUERY_INLINE);
        }

        if ($this -> queries -> get(BaseBuilder::QUERY_NESTED) == null){
            $this -> queries -> forget(BaseBuilder::QUERY_NESTED);
        }

        return $this -> queries -> toArray();
    }

    /**
     * Obtiene el listado de columnas
     * @return array
     */
    public function toColumns() : array {
        return $this -> columns -> toArray();
    }

}
