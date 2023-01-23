<?php

namespace Azit\Ddd\Controller;

use Azit\Ddd\Arch\Domains\Response\BaseResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

abstract class ResponseController extends Controller {
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Respuesta de documentos
     * @param BaseResponse $response
     * @return mixed
     */
    protected abstract function getResponseDocument(BaseResponse $response);

    /**
     * Respuiesta de datos
     * @param string $message
     * @param mixed $data
     * @param int $emptyCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function getResponseData(string $message, mixed $data, int $emptyCode = Response::HTTP_ACCEPTED){
        $response = ['data' => $data, 'message' => $message];

        if (!isset($data)) {
            return response() -> json($response, $emptyCode);
        }

        return response() -> json($response);
    }

}
