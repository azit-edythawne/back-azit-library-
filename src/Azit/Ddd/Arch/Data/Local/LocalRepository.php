<?php

namespace Azit\Ddd\Arch\Data\Local;

use Azit\Ddd\Arch\Constant\LibraryConstant;
use Azit\Ddd\Arch\Data\Local\Callback\GetPaginatedIterator;

abstract class LocalRepository {

    protected GetPaginatedIterator $paginated;

    protected function requiredPagination(GetPaginatedIterator $class) {
        $this->paginated = $class;
    }

    public function getPaginated(?array $filters = null) : array {
        $pages = collect($this->paginated->setPaginated($filters) -> toArray());

        return [
            LibraryConstant::PAGINATION_KEY_DATA => $pages -> pull(LibraryConstant::PAGINATION_KEY_DATA, []),
            LibraryConstant::PAGINATION_KEY_PAGES  => $pages
        ];
    }

}
