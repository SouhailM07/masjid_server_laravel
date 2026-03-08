<?php

namespace App\Helpers\Api;

interface ApiResponseInterface {
    public function notFoundResponse($msg=null): array;
    public function createResponse(array $data=[]): array;
    public function deleteResponse(array $data=[]): array;
    public function updateResponse(array $data=[]): array;
    public function idRequiredResponse($msg=null): array;
}