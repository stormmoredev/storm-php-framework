<?php

namespace Stormmore\Framework\Mvc\IO;

class Redirect
{
    public ?string $location = null;
    public ?string $body = null;

    public function __construct(string $url)
    {
        $this->location = $url;
    }
}