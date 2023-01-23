<?php

namespace Azit\Ddd\Arch\Data\Local\Callback;

use Illuminate\Pagination\AbstractPaginator;

interface GetPaginatedIterator {

    public function setPaginated(?array $filters = null) : AbstractPaginator;

}
