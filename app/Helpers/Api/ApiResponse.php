<?php

namespace App\Helpers\Api;

interface ApiResponseInterface {
    public function notFoundResponse($msg=null):array;
    public function createResponse(array $data=[]):array;
    public function deleteResponse(array $data=[]):array;
    public function updateResponse(array $data=[]):array;
    public function idRequiredResponse($msg=null):array;
};

class ApiResponse implements ApiResponseInterface{
    protected string $name;

    public function __construct(string $name = "Item") {
        $this->name = $name;
    }
    public function createResponse(array $data=[]):array{
        $msg=$data['msg'] ?? "$this->name was created";
        $res= $data['res'] ?? [];

        $response = [
            "message"=>$msg,
            ...$res
        ];

        return [$response,201];
    }

    public function notFoundResponse($msg=null):array{
        $msg=$msg ?? "$this->name was not found";

        $response =[
            "message"=>$msg 
        ];

        return  [$response,404];
    }

    public function updateResponse(array $data=[]):array{
        $msg=$data['msg']??"$this->name was updated";
        $res= $data['res'] ?? [];
        $response = [
            "message"=>$msg,
            ...$res
        ];

        return [$response,200];
    }

    public function deleteResponse(array $data=[]):array{
        $msg=$data['msg']??"$this->name was deleted";
        $res= $data['res'] ?? [];
        $response = [
            "message"=>$msg,
            'res'=>$res
        ];

        return [$response,200];
    }


    public function idRequiredResponse($msg=null):array{
        $msg=$msg??"$this->name Id is Required";
        $response=[
            "message"=>$msg
        ];

        return [$response,402];
    }

}