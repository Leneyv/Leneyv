<?php


namespace Tinggmagento\Tingg\Model;


class Ecryption {
    private $secret;
    private $algo;
    private $IV;

    public function __construct($secret, $IV, $algo='AES-256-CBC') {
        $this->secret = $secret;
        $this->algo = $algo;
        $this->IV = $IV;
    }

    public function encrypt($requestBody) {
        $secret = hash('sha256', $this->secret);
        $IV = substr(hash('sha256', $this->IV), 0, 16);

        $payload = json_encode($requestBody);
        $result = openssl_encrypt(
            $payload,
            $this->algo,
            $secret,
            0,
            $IV
        );

        return base64_encode($result);
    }
}

