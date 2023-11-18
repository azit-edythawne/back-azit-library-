<?php

namespace Azit\Ddd\Model;

use Azit\Ddd\Arch\Constant\PageConstant;
use Azit\Ddd\Arch\Constant\ValueConstant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Arr;

class BaseBuilderCompact {

    public const ORDER_ASC = 'asc';
    public const ORDER_DESC = 'desc';

    public const IN = 'In';
    public const IN_NOT = 'NotIn';

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
        $inline = Arr::get($filters, BaseBuilder::QUERY_INLINE, []);
        $nested = Arr::get($filters, BaseBuilder::QUERY_NESTED, []);

        if (count($inline) > ValueConstant::ARRAY_SIZE_EMPTY) {
            collect($inline) -> each(function ($rowQueries) use ($builder) {
                $this -> applyFilter($builder, $rowQueries, true);
            });
        }


        if (count($nested) > ValueConstant::ARRAY_SIZE_EMPTY) {
            $this -> builder -> where(function (Builder $query) use ($nested) {
                collect($nested) -> each(function ($rowQueries) use ($query) {
                    $this -> applyFilter($query, $rowQueries);
                });
            });
        }

        return $this;
    }

    /**
     * Permite aplicar filtros where
     * @param array $filters
     * @return $this
     */
    public function addQueries(array $filters) : BaseBuilderCompact {
        $this -> builder -> where($filters);
        return $this;
    }

    /**
     * Cargar nuevas relaciones al builder
     * @param array $relations
     * @return Model
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
     * @return void
     */
    private function applyFilter(Builder $query, array $rowQueries, bool $isBuilderReload = false) {
        $isRelation = Arr::get($rowQueries, 'relation');
        $table = Arr::get($rowQueries, 'table');
        $columns = Arr::get($rowQueries, 'columns');
        $boolean = Arr::get($rowQueries, 'boolean', 'and');

        if ($isRelation) {
            $query -> has($table, '>=', 1, $boolean, function (Builder $query) use ($columns) {
                $this -> whereType($query, $columns);
                //$query -> where($columns);
            });
        } else {
            $this -> whereType($query, $columns);
            //$query -> where($columns);
        }

        if ($isBuilderReload) {
            $this -> builder = $query;
        }
    }

    /**
     * Miultiples where
     * @param Builder $builder
     * @param array $columns
     */
    private function whereType(Builder $builder, array $columns) : void {
        foreach ($columns as $column) {
            $operator = $column[1];

            if ($operator == self::IN){
                $builder -> whereIn($column[0], $column[2], $column[3]);
            }

            if ($operator == self::IN_NOT) {
                $builder -> whereIn($column[0], $column[2], $column[3], true);
            }

            if ($operator != self::IN && $operator != self::IN_NOT) {
                $builder -> where($column[0], $column[1], $column[2], $column[3]);
            }
        }
    }

}