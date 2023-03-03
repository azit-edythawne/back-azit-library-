<?php

namespace Azit\Ddd\Arch\Domains\UseCases;

use Azit\Ddd\Arch\Constant\ValueConstant;
use Azit\Ddd\Arch\Domains\Response\BaseResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class BaseCases {

    private ?string $url;
    protected ?array $user;
    protected ?array $attributes;
    private BaseResponse $resource;
    private ?UserRequireCallback $userCallback;

    abstract protected function getUser() : ?array;

    /**
     * Constructor
     * Permite un objecto response opcional
     * Permite un user callback para obtener el usuario logeado
     * @param BaseResponse|null $object
     * @param UserRequireCallback|null $callback
     */
    public function __construct(?BaseResponse $object, ?UserRequireCallback $callback) {
        $this -> initResponse($object);
        $this -> userCallback = $callback;
    }

    /**
     * Se require inicializar objecto de respuesta
     * @param BaseResponse|null $object
     * @return void
     */
    private function initResponse(?BaseResponse $object) {
        if (isset($object)) {
            $this -> resource = $object;
        }

        if (!isset($object)) {
            $this -> resource = new BaseResponse();
        }
    }

    public function setRequest(Request $args){
        $this -> setAttributes($args -> all());
        $this -> url = $args -> url();

        // Se requiere informacion del usuario autenticado
        if (isset($this -> userCallback)) {
            $this -> user = $this -> userCallback -> extractUserByRequest($args);
        }
    }

    /**
     * @param array|null $attributes
     * @deprecated Este metodo esta obsoleto, aunque puede ser utilizado aun
     */
    protected function setAttributes(?array $attributes): void {
        $this -> attributes = $attributes;
    }

    /**
     * Retorna la respuesta
     * @param string $message
     * @param mixed $data
     * @return BaseResponse
     */
    protected function setResponse(string $message, mixed $data) : BaseResponse {
        $this -> resource -> setData($data);
        $this -> resource -> setMessage($message);
        return $this -> resource;
    }

    /**
     * Obtiene un valor del array de atributo dado un key
     * @param string $key
     * @return mixed
     */
    protected function getValue(string $key) : mixed {
        if (Arr::has($this -> attributes, $key)) {
            return Arr::get($this -> attributes, $key);
        }

        return null;
    }

    /**
     * Validar que la request tenga una key
     * @param string $key
     * @return bool
     */
    protected function hasKey(string $key) : bool {
        return Arr::has($this -> attributes, $key);
    }

    /**
     * Obtiene un valor numerico
     * @param string $key
     * @return int
     */
    protected function getNumericValue(string $key) : int {
        return Arr::get($this->attributes, $key, ValueConstant::DEFAULT_NUMERIC);
    }

    /**
     * Obtiene un valor string
     * @param string $key
     * @return string
     */
    protected function getStringValue(string $key) : string {
        return Arr::get($this->attributes, $key, ValueConstant::DEFAULT_STRING);
    }

    /**
     * Obtiene un valor array
     * @param string $key
     * @return Collection
     */
    protected function getArrayValue(string $key) : Collection {
        return collect(Arr::get($this->attributes, $key));
    }

    /**
     * Obtiene un array
     * @param string $key
     * @param int $position
     * @return UploadedFile
     */
    protected function getAttachment(string $key, int $position = 0) : UploadedFile {
        $value = Arr::get($this->attributes, $key);

        if (is_array($value)) {
            return $value[$position];
        }

        if ($value instanceof UploadedFile) {
            return $value;
        }
    }

    protected function getUrlLastSegment(): string {
        return Str::of($this->url)->basename();
    }

}
