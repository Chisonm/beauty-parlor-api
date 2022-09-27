<?php

namespace App\Helpers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ApiHelper
{
    /** Return valid api response
     * @param string|null $message
     * @param null $data
     * @param null $code
     * @param null $request
     * @return JsonResponse
     */
    public static function validResponse(string $message = null, $data = null, $code = null, $request = null): JsonResponse
    {
        if (is_null($data) || empty($data)) {
            $data = null;
        }
        $body = [
            'message' => $message,
            'data' => $data,
            'success' => true,
            'code' => $code ?? Response::HTTP_OK,

        ];

        return response()->json($body)->setStatusCode($code ?? Response::HTTP_OK);
    }

    public static function invalidResponse(string $message = null, int $status_code, Request $request = null, Exception $trace = null): JsonResponse
    {
        $code = ! empty($status_code) ? $status_code : null;
        $traceMsg = empty($trace) ? null : $trace->getMessage();

        $body = [
            'message' => $message,
            'code' => $code,
            'success' => false,
            'error_debug' => $traceMsg,
        ];

        ! empty($trace) ? logger($trace->getMessage(), $trace->getTrace()) : null;

        return response()->json($body)->setStatusCode($code);
    }

     /** Return error api response
      * @param string|null $message
      * @param int|null $status_code
      * @param Request|null $request
      * @param ValidationException|null $trace
      * @return JsonResponse
      */
     public static function inputErrorResponse(string $message = null, int $status_code = null, Request $request = null, ValidationException $trace = null): JsonResponse
     {
         $code = ($status_code != null) ? $status_code : '';
         $traceMsg = empty($trace) ? null : $trace->getMessage();
         $traceTrace = empty($trace) ? null : $trace->getTrace();

         $body = [
             'message' => $message,
             'code' => $code,
             'success' => false,
             'errors' => empty($trace) ? null : $trace->errors(),
         ];

         return response()->json($body)->setStatusCode($code);
     }

     public static function dataNotFound()
     {
         $message = 'data not found';

         return self::invalidResponse($message, Response::HTTP_NOT_FOUND);
     }
}
