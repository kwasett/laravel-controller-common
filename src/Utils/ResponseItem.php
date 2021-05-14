<?php

namespace Kwasett\LaravelCommon\Utils;

use Illuminate\Http\Response;

class ResponseItem
{

    public $responseCode;
    public $isError;
    public $data;
    public $statusDescription;
    public $extraData;
    public $errors = array();

    public function __construct(bool $isError, $responseCode, $description="", $data= [])
    {
        $this->isError = $isError;
        $this->responseCode = $responseCode;
        $this->statusDescription = $description;
        $this->data = $data;
    }



    public static function successResponse ($description = "", $data = []) : ResponseItem
    {
        return new ResponseItem(false, Response::HTTP_OK, $description, $data);
    }

    public static function createdResponse ($description = "", $data = []) : ResponseItem
    {
        return new ResponseItem(false, Response::HTTP_CREATED, $description, $data);
    }

    public static function badDataResponse ($description = "", $data = []) : ResponseItem
    {
        return new ResponseItem(true, Response::HTTP_BAD_REQUEST, $description, $data);
    }

    public static function notAuthorisedResponse ($description = "", $data = []) : ResponseItem
    {
        return new ResponseItem(true, Response::HTTP_UNAUTHORIZED, $description, $data);
    }

    public static function notFoundResponse ($description = "", $data = []) : ResponseItem
    {
        return new ResponseItem(true, Response::HTTP_NOT_FOUND, $description, $data);
    }







}
