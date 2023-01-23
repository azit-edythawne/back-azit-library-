<?php

namespace Azit\Ddd\Arch\Data\Local\Callback;

interface UpdateIterator {

    public function updateById(array $attributes, int $id) : ?array;

    public function deleteById(int $id) : bool;

}
