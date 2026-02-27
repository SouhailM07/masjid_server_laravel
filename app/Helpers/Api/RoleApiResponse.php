<?php

namespace App\Helpers\Api;

class RoleApiResponse extends ApiResponse{
    public function __construct()
    {
        return parent::__construct("Role");
    }
}