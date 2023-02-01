<?php

namespace Azit\Ddd\Model;

use Azit\Ddd\Arch\Constant\PageConstant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Arr;

class BaseBuilder {

    // Queries
    public const RELATION_HOST = 'host';
    public const QUERY_INLINE = 1;
    public const QUERY_NESTED = 2;
    public const ACTION_ACCEPT = 1;
    public const ACTION_REJECT = 2;

    // Builder
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
     * @return BaseBuilder
     */
    public static function of(Model $model){
        return new BaseBuilder($model ->newQuery());
    }

    /**
     * Comenzar con un builder
     * @param Builder $builder
     * @return BaseBuilder
     */
    public static function with(Builder $builder){
        return new BaseBuilder($builder);
    }

    /**
     * Base Query
     * @param array $relations
     * @param int|null $id
     * @return $this
     */
    public function baseQuery(array $relations = [], int $id = null) : BaseBuilder {
        $this -> builder -> with($relations);

        if (isset($id)) {
            $this -> builder -> where('id', $id) -> take(1);
        }

        return $this;
    }

    /**
     * Activo
     * @param bool $isActive
     * @return $this
     */
    public function isActive(bool $isActive = true) : BaseBuilder {
        $this -> builder -> where('active', $isActive);
        return $this;
    }

    /**
     * Order by por id asc o desc
     * @param bool $isOrderDesc
     * @return $this
     */
    public function orderById(bool $isOrderDesc = false) : BaseBuilder {
        $this -> builder -> orderBy('id', $isOrderDesc ? 'desc' : 'asc');
        return $this;
    }

    /**
     * Filtro
     * @param array|null $filters
     * @return $this
     */
    public function setFilter(array $filters = null) : BaseBuilder {
        if (!isset($filters)) {
            return $this;
        }

        $builder = $this -> builder;
        $inline = Arr::get($filters, BaseBuilder::QUERY_INLINE, []);
        $nested = Arr::get($filters, BaseBuilder::QUERY_NESTED, []);

        collect($inline) -> each(function ($rowQueries) use ($builder) {
            $this -> applyFilter($builder, $rowQueries);
        });

        if (count($nested) > 0) {
            $this -> builder -> where(function (Builder $query) use ($nested) {
                collect($nested) -> each(function ($rowQueries) use ($query) {
                    $this -> applyFilter($query, $rowQueries);
                });
            });
        }

        return $this;
    }

    /**
     * Aplicar filtro de busqueda
     * @param Builder $query
     * @param array $rowQueries
     * @return void
     */
    protected function applyFilter(Builder $query, array $rowQueries) {
        $isRelation = Arr::get($rowQueries, 'relation');
        $table = Arr::get($rowQueries, 'table');
        $columns = Arr::get($rowQueries, 'columns');
        $boolean = Arr::get($rowQueries, 'boolean', 'and');

        if ($isRelation) {
            $query -> has($table, '>=', 1, $boolean, function (Builder $query) use ($columns) {
                $query -> where($columns);
            });
        } else {
            $query -> where($columns);
        }
    }


    /**
     * Cargar nuevas relaciones
     * @param array $relations
     * @return Model
     */
    public function reloadRelations(array $relations) : Model {
        return $this -> builder -> getModel() -> load($relations);
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

}