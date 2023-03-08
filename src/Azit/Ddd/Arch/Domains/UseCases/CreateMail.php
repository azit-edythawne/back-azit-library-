<?php

namespace Azit\Ddd\Arch\Domains\UseCases;

use Azit\Ddd\Arch\Data\Network\MailRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Mail\PendingMail;
use Illuminate\Support\Facades\Mail;

class CreateMail {

    protected PendingMail $mail;
    protected MailRepository $repository;

    private function __construct(string $destiny){
        $this -> mail = Mail::to($destiny);
    }

    public static function to(string $destiny) {
        return new CreateMail($destiny);
    }

    public function with(string $view, string $subject, array $data = []) : CreateMail {
        $this -> repository = new MailRepository($view, $subject, $data);
        return $this;
    }

    public function addAttachments(array $attachments) : CreateMail {
        collect($attachments) -> each(function (UploadedFile $row) {
            $this -> repository -> attach($row, ['as' =>  $row -> getClientOriginalName()]);
        });

        return $this;
    }

    public function send() : bool {
        $response = $this -> mail -> send($this->repository);
        return isset($response);
    }

}
