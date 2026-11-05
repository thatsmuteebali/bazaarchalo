<?php

namespace App\Models\Concerns;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

trait HasEncryptedRouteKey
{
    /** Value placed in URLs instead of the real id. */
    public function getRouteKey()
    {
        $encrypted = Crypt::encryptString((string) $this->getKey());

        // URL-safe (standard base64 can contain "/" and "+")
        return rtrim(strtr($encrypted, '+/', '-_'), '=');
    }

    /** Normal binding (trashed rows are NOT found). */
    public function resolveRouteBinding($value, $field = null)
    {
        if ($field) {
            return parent::resolveRouteBinding($value, $field);
        }

        return $this->where($this->getKeyName(), $this->decryptRouteValue($value))
            ->firstOrFail();
    }

    /** Binding for routes using ->withTrashed() (restore / force delete). */
    public function resolveSoftDeletableRouteBinding($value, $field = null)
    {
        if ($field) {
            return parent::resolveSoftDeletableRouteBinding($value, $field);
        }

        return static::withTrashed()
            ->where($this->getKeyName(), $this->decryptRouteValue($value))
            ->firstOrFail();
    }

    private function decryptRouteValue($value): string
    {
        try {
            return Crypt::decryptString(strtr((string) $value, '-_', '+/'));
        } catch (DecryptException $e) {
            abort(404);
        }
    }
}
