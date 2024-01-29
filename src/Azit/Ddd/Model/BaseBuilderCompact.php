<?php

namespace Azit\Ddd\Model;

use Azit\Ddd\Arch\Constant\PageConstant;
use Azit\Ddd\Arch\Constant\ValueConstant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Database\Eloquent\Builder as ContractBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Arr;

class BaseBuilderCompact {

    public const ORDER_ASC = 'asc';
    public const ORDER_DESC = 'desc';

    private const QUERY_INLINE = 1;
    private const QUERY_NESTED = 2;

    public const OP_IN = 'In';
    public const OP_IN_NOT = 'NotIn';

    public const WHERE = 1;
    public const WITH = 2;
    public const WHERE_FN = 2;

    public const OP_ILIKE = 'ilike';
    public const OP_EQUAL = '=';
    public const AND = 'and';
    public const OR = 'or';
    public const OP_RAW = 'where_raw';


    protected Builder $builder;

    /**
     * Constructor
     * @param Builder $builder
     */
    private function __construct(Builder $builder) {
        $this -> builder = $builder;
    }

    /**
     * Comenzar con un modelo
     * @param Model $model
     * @return BaseBuilderCompact
     */
    public static function of(Model $model){
        return new BaseBuilderCompact($model ->newQuery());
    }

    /**
     * Comenzar con un builder
     * @param Builder $builder
     * @return BaseBuilderCompact
     */
    public static function with(Builder $builder){
        return new BaseBuilderCompact($builder);
    }

    /**
     * Permite agregar relaciones al builder actual
     * @param array $relations
     * @return $this
     */
    public function addRelations(array $relations = []) : BaseBuilderCompact {
        $this -> builder -> with($relations);
        return $this;
    }

    /**
     * Permite seleccionar las columnas que desee del modelo actual
     * @param array $selects
     * @return $this
     */
    public function select(array $selects = ['*']) : BaseBuilderCompact {
        $this -> builder -> select($selects);
        return $this;
    }

    /**
     * Permite aplicar un orderBy
     * @param string $orderBy
     * @param string $direction
     * @return $this
     */
    public function orderBy(string $orderBy = 'id', string $direction = self::ORDER_DESC) : BaseBuilderCompact {
        $this -> builder -> orderBy($orderBy, $direction);
        return $this;
    }

    /**
     * Filtra activos
     * @param bool $isActive
     * @return $this
     */
    public function isActive(?bool $isActive = null) : BaseBuilderCompact {
        if (isset($isActive)) {
            $this -> builder -> where('active', $isActive);
        }

        return $this;
    }

    /**
     * Permite trabajar con consultas anidadas
     * @param array|null $filters
     * @return $this
     */
    public function addMultiQueries(?array $filters = null) : BaseBuilderCompact {
        if (!isset($filters)) {
            return $this;
        }

        $builder = $this -> builder;
        $inline = Arr::get($filters, self::QUERY_INLINE, []);
        $nested = Arr::get($filters, self::QUERY_NESTED, []);
        $nestedInline = collect();
        $nestedRelation = collect();

        if (count($inline) > ValueConstant::ARRAY_SIZE_EMPTY) {
            collect($inline) -> each(function ($rowQueries) use ($builder) {
                $this -> applyFilter($builder, $rowQueries, true);
            });
        }

        if (count($nested) > ValueConstant::ARRAY_SIZE_EMPTY) {
            collect($nested) -> each(function ($rowQueries) use ($nestedInline, $nestedRelation) {
                $isRelation = Arr::get($rowQueries, 'relation');

                if ($isRelation) {
                    $nestedRelation -> push($rowQueries);
                }

                if (!$isRelation) {
                    $nestedInline -> push($rowQueries);
                }
            });

            if ($nestedInline -> count() > ValueConstant::ARRAY_SIZE_EMPTY) {
                $this -> builder -> where(function (Builder $query) use ($nestedInline) {
                    collect($nestedInline) -> each(function ($rowQueries) use ($query) {
                        $this -> applyFilter($query, $rowQueries);
                    });
                });
            }

            if ($nestedRelation -> count() > ValueConstant::ARRAY_SIZE_EMPTY) {
                collect($nestedRelation) -> each(function ($rowQueries) use ($builder) {
                    $this -> applyFilter($builder, $rowQueries, true, true);
                });
            }
        }

        return $this;
    }

    /**
     * Permite aplicar filtros where
     * @param array $filters
     * @return $this
     */
    public function addQueries(array $filters) : BaseBuilderCompact {
        $this -> whereType($this -> builder, $filters);
        return $this;
    }

    /**
     * Cargar nuevas relaciones al builder
     * @param array $relations
     * @return BaseBuilderCompact
     */
    public function reloadRelations(array $relations) : BaseBuilderCompact {
        $model = $this -> builder -> getModel() -> load($relations);
        return BaseBuilderCompact::of($model);
    }


    /**
     * Permite obtener el primer registro
     * @return Builder|Model
     */
    public function getFirst() : Builder | Model {
        return $this -> builder -> firstOrFail();
    }

    /**
     * Obtener builder
     * @return Builder
     */
    public function getBuilder() : Builder {
        return $this -> builder;
    }

    /**
     * Obtener modelo
     * @return Model
     */
    public function getModel() : Model {
        return $this -> builder -> getModel();
    }

    /**
     * Obtiene paginador
     * @param int $perPage
     * @return AbstractPaginator
     */
    public function getPaginate(int $perPage = PageConstant::ROWS_PER_PAGE) : AbstractPaginator {
        return $this -> builder -> paginate($perPage) -> withQueryString();
    }

    /**
     * Obtiene el array
     * @param bool $requireFirst
     * @return array
     */
    public function toArray(bool $requireFirst = false) : array {
        if ($requireFirst) {
            $result = $this -> builder -> first();
            return isset($result) ? $result -> toArray() : [];
        }

        return $this -> builder -> get() -> toArray();
    }

    /**
     * Permite aplicar condicionales anidadas
     * @param Builder $query
     * @param array $rowQueries
     * @param bool $isBuilderReload
     * @param bool $isEager
     * @return void
     */
    private function applyFilter(Builder $query, array $rowQueries, bool $isBuilderReload = false, bool $isEager = false) {
        $isRelation = Arr::get($rowQueries, 'relation');
        $table = Arr::get($rowQueries, 'table');
        $columns = Arr::get($rowQueries, 'columns');
        $boolean = Arr::get($rowQueries, 'boolean', 'and');
        $selects = Arr::get($rowQueries, 'selects');

        if ($isRelation) {

            if (!$isEager){
                $query -> has($table, '>=', 1, $boolean, function (Builder $builder) use ($columns, $selects) {
                    $this -> whereType($builder, $columns, $selects);
                });
            }

            if ($isEager) {
                $callback = function (ContractBuilder $builder) use ($columns, $selects) {
                    $this -> whereType($builder, $columns, $selects);
                };

                if ($boolean == self::AND) {
                    $query -> withWhereHas($table, $callback);
                }

                if ($boolean == self::OR) {
                    $query -> orWhereHas($table, $callback) -> with($callback ? [$table => fn ($query) => $callback($query)] : $table);
                }
            }

        }

        if (!$isRelation) {
            $this -> whereType($query, $columns, $selects);
        }

        if ($isBuilderReload) {
            $this -> builder = $query;
        }
    }

    /**
     * Multiples where
     * @param ContractBuilder|Builder $builder
     * @param array $columns
     * @param array|null $selects
     * @return void
     */
    private function whereType(ContractBuilder|Builder $builder, array $columns, array $selects = null) : void {
        foreach ($columns as $column) {
            $operator = $column[1];

            if ($operator == self::OP_IN){
                $builder -> whereIn($column[0], $column[2], $column[3]);
            }

            if ($operator == self::OP_IN_NOT) {
                $builder -> whereIn($column[0], $column[2], $column[3], true);
            }

            if ($operator == self::OP_RAW) {
                $builder -> whereRaw($column[0], $column[2], $column[3]);
            }

            if ($operator != self::OP_IN && $operator != self::OP_IN_NOT && $operator != self::OP_RAW) {
                $builder -> where($column[0], $column[1], $column[2], $column[3]);
            }
        }

        if (isset($selects)){
            $builder -> select($selects);
        }
    }

}