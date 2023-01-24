<?php

namespace Azit\Ddd\Arch\Data\Local;

use Azit\Ddd\Arch\Constant\PageConstant;
use Azit\Ddd\Arch\Data\Local\Callback\GetPaginatedIterator;

abstract class LocalRepository {

    protected GetPaginatedIterator $paginated;

    protected function requiredPagination(GetPaginatedIterator $class) {
        $this->paginated = $class;
    }

    public function getPaginated(?array $filters = null) : array {
        $pages = collect($this->paginated->setPaginated($filters) -> toArray());

        return [
            PageConstant::PAGINATION_KEY_DATA => $pages -> pull(PageConstant::PAGINATION_KEY_DATA, []),
            PageConstant::PAGINATION_KEY_PAGES  => $pages
        ];
    }

}
