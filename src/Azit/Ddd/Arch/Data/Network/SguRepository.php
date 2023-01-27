<?php

namespace Azit\Ddd\Arch\Data\Network;

use Azit\Ddd\Arch\Constant\SGUConstant;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class SguRepository extends NetworkRepository {
    abstract public function getAppKey() : string;

    /**
     * Esta clase esta obsoleta en SGURepository
     * @deprecated
     * @param string $segment
     * @return string
     */
    public function getUrl(string $segment): string {
        // TODO: Implement getUrl() method.
    }

    /**
     * Permite realizar una peticion a SGU
     * @param string $method
     * @param string $url
     * @param array $headers
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function sguRequest(string $method, string $url, array $headers) : ?array {
        $response = $this -> genericGuzzle($method, $url, [], $headers);

        if (!isset($response) || !$response['ok']) {
            return null;
        }

        return $response;
    }

    /**
     * Autenticar usuario en SGU
     * @param string $keyApp
     * @param string $ssid
     * @return array|null
     * @throws GuzzleException
     */
    public function auth(string $keyApp, string $ssid) : ?array {
        $url = Str::replace('%s', $keyApp, SGUConstant::URL_SGU);
        return $this -> sguRequest(NetworkRepository::GET, $url, [ 'auth' => $ssid ]);
    }

    /**
     * Desvincular session de SGU
     * @param string $ssid
     * @return array|null
     * @throws GuzzleException
     */
    public function logout(string $ssid) : ?array {
        return  $this -> sguRequest(NetworkRepository::POST, SGUConstant::URL_SGU_LOGOUT, [ 'auth' => $ssid]);
    }

    /**
     * Busque por email, cct o curp
     * @param string $keyApp
     * @param string $filter
     * @param string $value
     * @return array|null
     * @throws GuzzleException
     */
    public function searchPerson(string $keyApp, string $filter, string $value) : ?array {
        $url = Str::replaceArray('%s', [$keyApp, $filter, $value], SGUConstant::URL_SGU_BY_PERSON);
        return $this -> sguRequest(NetworkRepository::GET, $url, []);
    }

    /**
     * Busqueda de cct
     * @param string $keyApp
     * @param string $cct
     * @return array|null
     * @throws GuzzleException
     */
    public function searchCtt(string $keyApp, string $cct) : ?array {
        $url = Str::replaceArray('%s', [$keyApp, $cct], SGUConstant::URL_SGU_BY_CTT);
        $data = $this -> sguRequest(NetworkRepository::GET, $url, []);

        if (Arr::has($data, 'schema')) {
            Arr::forget($data, 'schema');
        }

        return $data;
    }

}