<?php

namespace Azit\Ddd\Arch\Data\Local;

use Azit\Ddd\Arch\Constant\PageConstant;
use Azit\Ddd\Arch\Data\Local\Callback\GetPaginatedIterator;

abstract class LocalRepository {

    protected array $relations = [];
    protected GetPaginatedIterator $paginated;

    /**
     * Permite agregar relaciones al repositorio actual
     * @param array $relations
     */
    public function setRelations(array $relations): void {
        $this -> relations = $relations;
    }

    /**
     * Permite inicializar objecto paginador a la clase
     * @param GetPaginatedIterator $class
     * @return void
     */
    protected function requiredPagination(GetPaginatedIterator $class) {
        $this->paginated = $class;
    }

    /**
     * Obtiene consulta paginada
     * @param array|null $filters
     * @return array
     */
    public function getPaginated(?array $filters = null) : array {
        $pages = collect($this->paginated->setPaginated($filters) -> toArray());

        return [
            PageConstant::PAGINATION_KEY_DATA => $pages -> pull(PageConstant::PAGINATION_KEY_DATA, []),
            PageConstant::PAGINATION_KEY_PAGES  => $pages
        ];
    }

}
