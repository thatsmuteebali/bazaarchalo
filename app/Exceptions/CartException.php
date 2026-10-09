<?php

namespace App\Exceptions;

use RuntimeException;

/** A cart rule was broken (out of stock, product removed, options missing...). The message is safe to show to the customer. */
class CartException extends RuntimeException
{
    /** Optional page the browser should be sent to (e.g. the product page to choose options). */
    public ?string $redirect;

    public function __construct(string $message, ?string $redirect = null)
    {
        parent::__construct($message);

        $this->redirect = $redirect;
    }
}
