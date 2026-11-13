<?php

namespace App\Exceptions;

class KeycloakDirectoryException extends \RuntimeException
{
    public function __construct(public readonly ?int $httpStatus = null)
    {
        parent::__construct('Layanan pengguna Keycloak belum dapat memproses permintaan.');
    }
}
