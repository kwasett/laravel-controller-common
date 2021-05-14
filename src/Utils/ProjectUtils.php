<?php

namespace Kwasett\LaravelCommon\Utils;


use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProjectUtils
{

    public static function processResponse(ResponseItem $responseItem): array
    {
        if ($responseItem->isError) {
            $errors = $responseItem->errors;
            if (!$responseItem->errors) {
                $errors = [];
            }
            return [
                'error' => true,
                'description' => $responseItem->statusDescription,
                'errors' => $errors
            ];
        } else {
            return [
                'error' => false,
                'description' => $responseItem->statusDescription,
                'data' => $responseItem->data
            ];
        }
    }

    public static function traceId()
    {
        return self::uniqidReal() . time();
    }

    static function uniqidReal($lenght = 40)
    {
        // uniqid gives 13 chars, but you could adjust it to your needs.
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($lenght / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
        } else {
            throw new Exception("no cryptographically secure random function available");
        }
        return substr(bin2hex($bytes), 0, $lenght);
    }


    public static function validateInput($rules, $data): ResponseItem
    {
        $validator = Validator::make($data, $rules);

        $responseItem = ResponseItem::successResponse();
        if ($validator->fails()) {
            $responseItem->isError = true;
            $responseItem->responseCode = Response::HTTP_BAD_REQUEST;
            $responseItem->statusDescription = "Invalid Input";
            $responseItem->errors = (self::errorArray($validator->errors()));
            return $responseItem;
        }

        return $responseItem;
    }


    public static function errorArray($errors): array
    {
        $item = array();
        ProjectLog::debug(json_encode($errors));
        $errors = json_decode(json_encode($errors));

        foreach ($errors as $key => $value) {
            $inst=new \StdClass;
            $inst->$key = $value;
            ProjectLog::debug("Error $key:: " . json_encode($value));
            $item[] = $inst;
        }

        ProjectLog::debug("Item Total $key ::: ".json_encode($item));
        return $item;
    }


    static function hiddenFields()
    {
        return ['updated_at', 'created_by', 'updated_by', 'created_at'];
    }

    static function defaultAuditCreate()
    {
        $date = date('Y-m-d H:i:s');
        $user = Auth::user();
        return array_merge(self::defaultAuditEdit(), [
            'created_by' => $user->id,
            'created_at' => $date,
        ]);
    }

    static function defaultAuditEdit()
    {
        $user = Auth::user();
        return [
            'updated_by' => $user->id
        ];
    }


    static function populateNameIdCombo($resultSet)
    {
        $select = array();
        foreach ($resultSet as $item) {
            $select[$item->id] = $item->name;
        }

        return $select;
    }


    static function merchantStatus()
    {
        return self::merchantItemGroupBy('status');
    }

    static function merchantType()
    {
        return self::merchantItemGroupBy('user_type');
    }

    static function merchantItemGroupBy($field)
    {
        $q = "select $field as item from merchant_retailer group by $field";
        $resultSet = DB::select($q);

        $select = array();
        foreach ($resultSet as $item) {
            $select[$item->item] = $item->item;
        }

        return $select;
    }


    static function valueExistInArrayDefault($array, $index, $default)
    {
        return (isset($array[$index])) ? $array[$index] : $default;
    }

    static function generateVoucherCode($firstname, $lastname)
    {
        if (strlen($firstname) < 1 && strlen($lastname) < 1) {
            return "";
        }

        $prefix = strtoupper(substr($firstname, 0, 1) . substr($lastname, 0, 1));
        $genCode = self::generateNumericCode(5) . rand(10, 99);
        return strtoupper("{$prefix}{$genCode}");
    }
}
