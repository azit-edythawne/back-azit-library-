<?php

namespace Azit\Ddd\Arch\Domains\Response;

class BaseResponse {

    protected mixed $data;
    protected ?string $message;

    /**
     * @return mixed
     */
    public function getData(): mixed {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData(mixed $data): void {
        $this->data = $data;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string {
        return $this->message;
    }

    /**
     * @param string|null $message
     */
    public function setMessage(?string $message): void {
        $this->message = $message;
    }

}
