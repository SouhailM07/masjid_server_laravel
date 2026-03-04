<?php

namespace App\Helpers\Api;

class PrayerApiResponse extends ApiResponse{
    public function __construct()
    {
        return parent::__construct("Prayer");
    }
}