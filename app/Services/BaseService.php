<?php

namespace App\Services;

class BaseService
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message, $headers = [])
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data'    => $result,
        ];


        return response()->json($response, 200, $headers);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [])
    {
        $response = [
            'success' => false,
            'message' => $error['message'],
            'error_code'=> $error['code'],
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $error['http_code']);
    }

    protected function getErrorMessage($e)
    {
        return isset($e->message)? $e->message : $e->getMessage();
    }

    protected function getFilterParamters($request, $filterFields)
    {
        $filters = array();
        $request= $request->all();
        foreach ($request as $key => $value) {
            if (in_array($key, $filterFields)) {
                $filters[$key] = $value;
            }
        }
        return $filters;
    }

    protected function formatData($data)
    {
        if (isset($data->id)) {
            $data = $this->addExtraData([$data]);
            return array_pop($data);
        }
        $resultList = $this->addExtraData($data);
        if (!is_array($data) && property_exists($data, 'total')) {
            $result = $data->toArray();
            $result['data'] = $resultList;
            return $result;
        }
        return $resultList;
    }

    protected function addExtraData($data)
    {
        $resultList = array();
        foreach ($data as $item) {
            $resultList[] = $item->getOriginal();
        }
        return $resultList;
    }
}
