<?php

namespace Azit\Ddd\Arch\Data\Local\Callback;

interface CreateIterator {

    public function create(array $attributes) : ?array;

}
