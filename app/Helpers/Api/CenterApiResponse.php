<?php

namespace App\Helpers\Api;

class CenterApiResponse extends ApiResponse{
    public function __construct()
    {
        return parent::__construct("Center");
    }
}