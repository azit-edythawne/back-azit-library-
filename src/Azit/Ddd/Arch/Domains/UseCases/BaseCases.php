<?php

namespace Azit\Ddd\Arch\Domains\UseCases;

use Azit\Ddd\Arch\Constant\LibraryConstant;
use Azit\Ddd\Arch\Domains\Response\BaseResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class BaseCases {

    private ?string $url;
    protected ?array $attributes;
    private BaseResponse $resource;

    abstract protected function getUser() : ?array;

    public function __construct(BaseResponse $object = null) {
        $this -> initResponse($object);
    }

    /**
     * Se require inicializar objecto de respuesta
     * @param BaseResponse|null $object
     * @return void
     */
    private function initResponse(BaseResponse $object = null) {
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
        $this -> user = $args -> user() ?-> toArray();
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
        return $this->resource;
    }

    /**
     * Obtiene un valor numerico
     * @param string $key
     * @return int
     */
    protected function getNumericValue(string $key) : int {
        return Arr::get($this->attributes, $key, LibraryConstant::DEFAULT_NUMERIC);
    }

    /**
     * Obtiene un valor string
     * @param string $key
     * @return string
     */
    protected function getStringValue(string $key) : string {
        return Arr::get($this->attributes, $key, LibraryConstant::DEFAULT_STRING);
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
