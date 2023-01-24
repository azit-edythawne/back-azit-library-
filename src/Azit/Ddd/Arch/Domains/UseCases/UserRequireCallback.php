<?php

namespace Azit\Ddd\Arch\Domains\UseCases;

use Illuminate\Http\Request;

interface UserRequireCallback {

    public function extractUserByRequest(Request $args) : ?array;

    public function getUser() : ?array;

}