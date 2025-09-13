<?php

namespace src\App;

use Stormmore\Framework\Mvc\Attributes\Controller;
use Stormmore\Framework\Mvc\Attributes\Route;
use Stormmore\Framework\Mvc\View\View;

#[Controller]
readonly class HomepageController
{
    #[Route("/", "/homepage")]
    public function index(): View
    {
        return view("homepage");
    }
}