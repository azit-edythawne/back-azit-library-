<?php

namespace Azit\Ddd\Arch\Data\Network;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

abstract class NetworkRepository {

    abstract public function getUrl(string $segment) : string;

    public const GET = 'GET';
    public const POST = 'POST';


    /**
     * Permite consultar
     * @param string $url
     * @param array $formParam
     * @param array $headers
     * @return \Psr\Http\Message\StreamInterface|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getPostGuzzle(string $url, array $formParam = [], array $headers = []) {
        try {
            $client = new Client();
            $response = $client -> request(self::POST, $url, [
                'form_params' => $formParam,
                'headers' => $headers
            ]);

            if ($response -> getStatusCode() == Response::HTTP_OK) {
                return $response -> getBody();
            }
        } catch (Exception $exception) {

        }

        return null;
    }

    /**
     * Permite obtener información mediante post de HttpClient
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return null
     */
    protected function getPostHttp(string $url, array $data = [], array $headers = []){
        try {
            $response = Http::withHeaders($headers) -> post($url, $data);

            if ($response -> status() == Response::HTTP_OK) {
                return $response -> object() -> data;
            }

        } catch (Exception $exception) {

        }

        return null;
    }

    /**
     * Obtiene un string content para crear un archivo con la respuesta
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return string|null
     */
    protected function getGetAttachmentHttp(string $url, array $data = [], array $headers = []) {
        try {
            $response = Http::withHeaders($headers) -> get($url, $data);

            if ($response -> status() == Response::HTTP_OK) {
                return $response -> body();
            }

        } catch (Exception $exception) {

        }

        return null;
    }

    /**
     * Permite subir archivos atraves de HttpClient y post
     * @param string $url
     * @param string $filename
     * @param array $data
     * @param UploadedFile $file
     * @param array $headers
     * @return null
     */
    protected function setPostAttachmentHttp(string $url, string $filename, array $data, UploadedFile $file, array $headers = []) {
        try {
            $response = Http::withHeaders($headers) -> attach('file', file_get_contents($file), $filename) -> post($url, $data);

            if ($response -> status() == Response::HTTP_OK) {
                return $response -> object() -> data;
            }
        } catch (Exception $exception) {

        }

        return null;
    }

    /**
     * Metodo generico en Guzzle que permite realizar solicitudes
     * @param string $method
     * @param string $url
     * @param array $formParam
     * @param array $headers
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function genericGuzzle(string $method, string $url, array $formParam = [], array $headers = []) {
        try {
            $client = new Client();
            $response = $client -> request($method, $url, [
                'form_params' => $formParam,
                'headers' => $headers
            ]);

            if ($response -> getStatusCode() == Response::HTTP_OK) {
                return json_decode($response -> getBody(), true);
            }
        } catch (Exception $exception) {

        }

        return null;
    }


}
