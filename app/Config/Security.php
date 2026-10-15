<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Security extends BaseConfig
{
    public string $csrfProtection = 'session';

    /**
     * 요청마다 토큰 재생성 안 함 — 랜딩의 연속 fetch (PayPal create → capture)가
     * 같은 토큰을 재사용할 수 있어야 한다.
     */
    public bool $regenerate = false;

    public string $tokenName  = 'csrf_token';
    public string $headerName = 'X-CSRF-TOKEN';
    public string $cookieName = 'csrf_cookie';
    public int $expires       = 7200;
    public bool $tokenRandomize = false;
    public string $samesite   = 'Lax';
    public bool $redirect     = ENVIRONMENT === 'production';
}
