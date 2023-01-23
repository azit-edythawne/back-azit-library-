<?php

namespace Azit\Ddd\Arch\Data\Local\Callback;

interface GetIterator {

    public function get(int $id) : ?array;

    public function getAll() : ?array;

}
