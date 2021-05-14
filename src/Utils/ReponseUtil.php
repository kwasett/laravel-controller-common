<?php
/**
 * Created by PhpStorm.
 * User: andela
 * Date: 13/12/2019
 * Time: 12:55 AM
 */

namespace Kwasett\LaravelCommon\Utils;


use Illuminate\Http\Response;

class ReponseUtil
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


    public static function returnedResponse(ResponseItem $response)
    {
        $data = self::processResponse($response);
        ProjectLog::debug("ResponseItem : Data " . json_encode($data));
        ProjectLog::debug("Done ResponseCode ({$response->responseCode}) :: {@$response->statusDescription}");
        return response()->json(
            $data,
            $response->responseCode);
    }


    public static final function searchResponse($data, $itemName = "data")
    {
        if (null == $data) {
            $reponse = new ResponseItem(true, Response::HTTP_NOT_FOUND, "Empty", $data);

        } else
            $reponse = new ResponseItem(false, Response::HTTP_OK, "Found", [$itemName => $data]);

        return self::returnedResponse($reponse);
    }
}
