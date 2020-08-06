<?php

namespace PHPExpress\Security;

use Exception;

class JWT
{
    public static $config = [
        'type'          => 'JWT',
        'alg'           => 'HS256',
        'hash'          => 'sha256',
        'secret'        => '',
        'expiration_at' => '',
        'message' => [
            'iss'       => 'O emissor do token não foi reconhecido.',
            'exp'       => 'Token expirado!.',
            'sub'       => 'Este token não lhe pertence.',
            'default'   => 'Token inválido.'
        ],
    ];

    protected static function prepare($token, $json = true)
    {
        $token = ($json) ? json_encode($token) : $token;

        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($token));
    }

    public static function encode($payload, $secret = '')
    {
        if (!empty($secret)) {
            self::$config['secret'] = $secret;
        }

        $header = self::prepare([
            'typ' => self::$config['type'],
            'alg' => self::$config['alg']
        ]);

        $defaultPayload = [
            // issuer: Emissor do token
            'iss' => md5($_SERVER['HTTP_HOST']),
            // issued at: Quando o token foi criado
            'iat' => time(),
            // expiration: Quando o token expira (Opcional)
            'exp' => (!empty(self::$config['expiration_at'])) ? strtotime(self::$config['expiration_at'], time()) : '',
            // subject: De quem o token pertence
            'sub' => md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']),
            // audience: Destinatario do token
            'aud' => '',
        ];

        $payload = self::prepare(array_merge($defaultPayload, $payload));

        $signature = self::prepare(hash_hmac(self::$config['hash'], "$header.$payload", self::$config['secret'], true), false);

        return "$header.$payload.$signature";
    }

    public static function decode($token, $secret = '')
    {
        if (!empty($secret)) {
            self::$config['secret'] = $secret;
        }

        $parts = explode('.', $token);

        if (sizeof($parts) == 1 && strlen($parts[0]) > 0) {
            throw new Exception("Não é um token (token: $token)");
        }

        $header    = $parts[0];
        $payload   = $parts[1];
        $signature = $parts[2];

        $signatureToken = self::prepare(hash_hmac(self::$config['hash'], "$header.$payload", self::$config['secret'], true), false);

        $payload   = json_decode(base64_decode($payload), true);

        if ($signatureToken === $signature) {
            if (!empty($payload['exp']) && ($payload['exp'] < time())) {
                return [
                    'error' => self::$config['message']['exp']
                ];
            }

            if (!empty($payload['sub']) && ($payload['sub'] !==  md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']))) {
                return [
                    'error' => self::$config['message']['sub']
                ];
            }

            if (!empty($payload['iss']) && ($payload['iss'] !== md5($_SERVER['HTTP_HOST']))) {
                return [
                    'error' => self::$config['message']['iss']
                ];
            }

            return $payload;
        }

        return [
            'error' => self::$config['message']['default']
        ];
    }
}
