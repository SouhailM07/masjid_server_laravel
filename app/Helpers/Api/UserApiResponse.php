<?php

namespace App\Helpers\Api;

class UserApiResponse extends ApiResponse{
    public function __construct()
    {
        return parent::__construct("User");
    }
}