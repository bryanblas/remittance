<?php


namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Support\Facades\Log;
use App\Http\Helpers\ErrorMessage;

class BaseController extends Controller
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
            'data'    => $result,
            'message' => $message
        ];


        return response()->json($response, 200, $headers);
    }

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendCodeResponse($result, $message, $headers = [], $code)
    {
        $response = [
            'success' => true,
            'code'    => $code,
            'message' => $message,
            'data'    => $result
        ];


        return response()->json($response, 200, $headers);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];


        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }
        // die('dars');

        return response()->json($response, $code);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendCodeError($error, $errorMessages = [], $code =1000)
    {
        $response = [
            'success' => false,
            'code'    => $code,
            'message' => $error
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response);
    }



    /**
     * Globally process the error exception
     * @param  Exception $e
     * @return json
     */
    public function processError($e, $returnMessage = false)
    {
        Log::info('Error reponse: ' . json_encode($e));
        if (method_exists($e, 'getResponse') && !is_null($e->getResponse())) {
            $response = json_decode((string) $e->getResponse()->getBody());
            Log::info('Error simplified: ' . $e->getResponse()->getBody());
            if (property_exists($response, 'message')) {
                $errorResponse = $response->message;
            } elseif ($e->getResponse()->getReasonPhrase()) {
                $errorResponse = $e->getResponse()->getReasonPhrase();
            } elseif (isset($response->error)) {
                $errorResponse = $response->error;
            }
        } elseif (isset($e->message)) {
            $errorResponse = $e->message;
        } elseif (isset($response->error)) {
            $errorResponse = $response->error;
        }

        $getCode = ErrorMessage::errorCode($errorResponse);

        $errorResponse = '[' . $getCode . '] ' . $errorResponse;

        Log::info('Error found: ' . $errorResponse);
        if ($returnMessage) {
            return $errorResponse;
        }
        return $this->sendError('Internal Error', $errorResponse);
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
