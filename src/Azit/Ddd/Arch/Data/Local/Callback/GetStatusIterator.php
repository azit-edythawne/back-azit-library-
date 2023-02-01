<?php

namespace Azit\Ddd\Arch\Data\Local\Callback;

use Azit\Ddd\Model\BaseBuilder;

interface GetStatusIterator {

    public function getNextStatus(int $id, array $idRoles, int $type = BaseBuilder::QUERY_INLINE, string $columnStatus = 'status_id');

}