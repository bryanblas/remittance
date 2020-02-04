<?php

namespace App\Services\KycDocument;

use Exception;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Exports\Excel;
use Illuminate\Support\Facades\Mail;
use App\Mail\KycDocument\DocumentSubmissionMailer;
use App\Mail\KycDocument\DocumentUpdateMailer;
use App\Http\Requests\StoreKycDocumentRequest;
use App\Http\Requests\UpdateKycDocumentRequest;
use App\Services\BaseService;
use App\Repositories\KycDocument\KycDocumentInterface;
use App\Repositories\Merchant\MerchantInterface;

class KycDocumentService extends BaseService
{
    /*LOGS*/
    const LOGS_CREATING = 'CREATING KYC DOCUMENT:';
    const LOGS_CREATED  = 'CREATED KYC DOCUMENT:';
    const LOGS_UPDATING = 'UPDATING KYC DOCUMENT:';
    const LOGS_UPDATED = 'UPDATED KYC DOCUMENT:';
    const LOGS_DELETING = 'DELETING KYC DOCUMENT:';
    const LOGS_DELETED = 'DELETED KYC DOCUMENT:';
    const LOGS_RETRIEVING = 'RETRIEVING KYC DOCUMENT:';
    const LOGS_RETRIEVED = 'RETRIEVED KYC DOCUMENT:';
    const LOGS_EXPORTING = 'EXPORTING KYC DOCUMENT:';
    const LOGS_UPDATE_STATUS = 'KYC DOCUMENT UPDATE STATUS:';

    /*DB FIELDS*/
    const DB_MERCHANT_ID_FIELD = 'merchant_id';
    const DB_DOCUMENT_TYPE_FIELD = 'document_type';
    const DB_TYPE_FIELD = 'type';
    const DB_FILENAME_FIELD = 'filename';
    const DB_STATUS_FIELD = 'status';
    const DB_FILESIZE_FIELD = 'filesize';
    const DB_REMARKS_FIELD = 'remarks';

    /*API ERROR RESPONSE*/
    const API_ERROR_INVALID_CREATE_REQUEST = array('code' => 'E-KYC-DOC-001' , 'message' => 'Invalid request', 'http_code' => 400);
    const API_ERROR_INVALID_UPDATE_REQUEST = array('code' => 'E-KYC-DOC-002' , 'message' => 'Invalid request', 'http_code' => 400);
    const API_ERROR_INVALID_FILE = array('code' => 'E-KYC-DOC-003' , 'message' => 'Unsupported File format', 'http_code' => 400);
    const API_ERROR_INVALID_USER = array('code' => 'E-KYC-DOC-004' , 'message' => 'User is not a valid merchant', 'http_code' => 400);
    const API_ERROR_NOT_FOUND = array('code' => 'E-KYC-DOC-404' , 'message' => 'KYC Document not found.', 'http_code' => 404 );
    const API_ERROR_UNEXPECTED = array('code' => 'E-KYC-DOC-500' , 'message' => 'An unexpected error has occurred', 'http_code' => 500);

    /*EXPORT FILE NAME*/
    const EXPORT_FILE_NAME = 'PCSO-Banks.xls';
    const EXPORT_HEADERS = ['Bank ID','Code','Name','Active Status','Date Updated','Date Created','Date Deleted'];

    protected $kycDocumentInterface;
    protected $merchantInterface;
    protected $excelExport;

    public function __construct(
        KycDocumentInterface $kycDocumentInterface,
        MerchantInterface $merchantInterface,
        Excel $excelExport
   ) {
        $this->kycDocumentInterface= $kycDocumentInterface;
        $this->merchantInterface = $merchantInterface;
        $this->excelExport = $excelExport;
    }

    public function create(Request $request)
    {
        try {
            Log::info(self::LOGS_CREATING . $request->user()->email . ' - ' . json_encode($request->all()));
            $validator = $this->validateCreateRequest($request);
            if ($validator->fails()) {
                Log::info(self::LOGS_CREATING . ' - ' . json_encode($validator->errors()));
                return $this->sendError(self::API_ERROR_INVALID_CREATE_REQUEST, $validator->errors());
            }
            $merchant = $this->merchantInterface->getByUserEmail($request->user()->email);
            if ($merchant->count()) {
                $data = $this->getKycDocumentPayload($request);
                $data[self::DB_MERCHANT_ID_FIELD] = $request->merchant_id;
                $document = $this->kycDocumentInterface->create($data);
                Mail::to($request->user()->email)->send(new DocumentSubmissionMailer($document));
                Log::info(self::LOGS_CREATED . $request->user() . ' - ' . json_encode($document));
                return $this->sendResponse($document->getOriginal(), '');
            }
            Log::info(self::LOGS_CREATING . ' - ' . self::API_ERROR_INVALID_USER['message']);
            return $this->sendError(self::API_ERROR_INVALID_USER, '');
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_CREATING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function update($id, Request $request)
    {
        try {
            Log::info(self::LOGS_UPDATING . $request->user()->email . ' - ' . json_encode($request->all()));
            $validator = $this->validateUpdateRequest($request);
            if ($validator->fails()) {
                Log::info(self::LOGS_UPDATING . ' - ' . json_encode($validator->errors()));
                return $this->sendError(self::API_ERROR_INVALID_UPDATE_REQUEST, $validator->errors());
            }
            $data = $this->getKycDocumentPayload($request->status);
            $document = $this->kycDocumentInterface->update($id, $data);
            if ($document) {
                Log::info(self::LOGS_UPDATED . $request->user() . ' - ' . json_encode($document));
                return $this->sendResponse($document->getOriginal(), '');
            }
            return $this->sendError(self::API_ERROR_NOT_FOUND, '');
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_CREATING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function updateStatus($id, Request $request)
    {
        try {
            Log::info(self::LOGS_UPDATING . $request->user()->email . ' - ' . json_encode($request->all()));
            $payload = $request->all();
            $data = [
                'status' => $request->status,
                'remarks' => $request->remarks
            ];
            $document = $this->kycDocumentInterface->update($id, $data);
            if ($document) {
                Log::info('TEST - ' . json_encode($document->Merchant->email));
                Mail::to($document->Merchant->email)->send(new DocumentUpdateMailer($document));
                Log::info(self::LOGS_UPDATED . $request->user() . ' - ' . json_encode($document));
                return $this->sendResponse($document->getOriginal(), '');
            }
            return $this->sendError(self::API_ERROR_NOT_FOUND, '');
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_UPDATE_STATUS . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function delete($id, $request)
    {
        try {
            Log::info(self::LOGS_DELETING . $request->user()->email . ' - ' . $id);
            $document = $this->kycDocumentInterface->delete($id);
            Log::info(self::LOGS_DELETED . $request->user() . ' - ' . $id);
            return $this->sendResponse([], '');
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_DELETING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function getByMerchant($id)
    {
        try {
            Log::info(self::LOGS_RETRIEVING . ' - ' . $id);
            $document = $this->kycDocumentInterface->getByMerchant($id);
            if ($document) {
                return $this->sendResponse($document->toArray(), '');
            } else {
                return $this->sendError(self::API_ERROR_NOT_FOUND, '');
            }
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_RETRIEVING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function getAll($request)
    {
        try {
            Log::info(self::LOGS_RETRIEVING);
            $document = $this->kycDocumentInterface->getAll($request);
            return $this->sendResponse($document->toArray(), '');
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_RETRIEVING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function export($request)
    {
        try {
            Log::info(self::LOGS_EXPORTING);
            $document = $this->kycDocumentInterface->getAll($request);
            return $this->excelExport->setData($document->toArray(), self::EXPORT_HEADERS)->download(self::EXPORT_FILE_NAME);
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_EXPORTING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    private function validateCreateRequest($request)
    {
        $storeKycDocumentRequest = new StoreKycDocumentRequest();
        $rules = $storeKycDocumentRequest->rules();
        return Validator::make($request->all(), $rules);
    }

    private function validateUpdateRequest($request)
    {
        $updateKycDocumentRequest = new UpdateKycDocumentRequest();
        $rules = $updateKycDocumentRequest->rules();
        return Validator::make($request->all(), $rules);
    }

    private function uploadDocument($request)
    {
        if ($request->document_file !== null) {
            $file['name'] = time().'.'.$request->document_file->getClientOriginalExtension();
            $file['size'] = $request->document_file->getSize();
            $file['type'] = $this->getFileType($request->document_file);

            if ($file['type'] == '') {
                throw new Exception('Invalid file type.');
            }

            if ($request->document_file->move(public_path(env('KYC_DOCUMENT_DIR')), $file['name'])) {
                return $file;
            }
        }
        return false;
    }

    private function getFileType($file)
    {
        $mimeType = $file->getClientMimeType();
        $type = '';

        if (strpos($mimeType, 'image') !== false) {
            $type = 'image';
        } elseif (strpos($mimeType, 'document') !== false || strpos($mimeType, 'text') !== false) {
            $type = 'document';
        } elseif (strpos($mimeType, 'pdf') !== false) {
            $type = 'pdf';
        }

        return $type;
    }

    private function getKycDocumentPayload($request)
    {
        $payload = [
            self::DB_DOCUMENT_TYPE_FIELD => $request->document_type,
            self::DB_STATUS_FIELD => $request->status,
            self::DB_REMARKS_FIELD => $request->remarks
        ];
        $document = $this->uploadDocument($request);

        if ($document !== false) {
            $payload[self::DB_FILENAME_FIELD] = $document['name'];
            $payload[self::DB_TYPE_FIELD] = $document['type'];
            $payload[self::DB_FILESIZE_FIELD] = $document['size'];
        }

        return $payload;
    }
}
