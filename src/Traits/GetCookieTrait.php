<?php

declare(strict_types=1);

namespace App\Traits;

use Psr\Http\Message\ServerRequestInterface;
use App\Helper\CryptMsg;
use App\Config\Env;


trait GetCookieTrait {
    /**
     * Take cookies and decrypt those that are encrypted
     */
    protected function getCookies (ServerRequestInterface $request):array {
        try {
            $toEncrypt = explode(',', Env::get('COOKIES_TO_ENCRYPT'));
            $expirationInDays = (int) Env::get('COOKIE_EXPIRY_IN_DAYS');

        } catch (\InvalidArgumentException $e) {
            die($e->getMessage());
        }
        
        $crypt = CryptMsg::instance();
        $cookies = $request->getCookieParams();

        foreach($cookies AS $name => $value) {
            if (in_array($name, $toEncrypt)) {
                $cookies[$name] = $crypt->decrypt($value, $crypt:: nonce());
            }
        }

        return $cookies;
    }

    /**
     * Method to make a time-based decision whether to refresh sas or not
     * @param array $cookies Result from $this->getCookies() method
     */
    protected function isLastLoginNearExpiry(array $cookies) {
        $lastLogin = new \DateTime($cookies['logindate']);
        $now = new \DateTime(date('Y-m-d H:i:s'));
        $interval = (int) $lastLogin->diff($now)->format('%R%a');

        if ($interval < 0) {
            throw new \InvalidArgumentException('Invalid session.');
        }

        if ($interval < $this->expirationInDays / 2) {
            return false;
        }

        return true;

    }
}