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

    /**
     * Destinatario
     * @param string $destiny
     * @return CreateMail
     */
    public static function to(string $destiny) : CreateMail {
        return new CreateMail($destiny);
    }

    /**
     * Se agregara la informacion que se le quiere enviar al destino por correo
     * @param string $view
     * @param string $subject
     * @param array $data
     * @return $this
     */
    public function with(string $view, string $subject, array $data = []) : CreateMail {
        $this -> repository = new MailRepository($view, $subject, $data);
        return $this;
    }

    /**
     * Agregar documentos de tipo UploadedFile
     * Nota: Llamar este metodo seguido de
     * @link CreateMail::with
     * @param array $attachments
     * @return $this
     */
    public function addAttachments(array $attachments) : CreateMail {
        collect($attachments) -> each(function (UploadedFile $row) {
            $this -> repository -> attach($row, ['as' =>  $row -> getClientOriginalName()]);
        });

        return $this;
    }

    /**
     * Agregar usuarios a los que se enviaran copias del correo original
     * @param array $users
     * @return $this
     */
    public function cc(array $users): CreateMail {
        $this -> mail -> cc($users);
        return $this;
    }

    /**
     * Enviar correo
     * @return bool
     */
    public function send() : bool {
        $response = $this -> mail -> send($this->repository);
        return isset($response);
    }

}
