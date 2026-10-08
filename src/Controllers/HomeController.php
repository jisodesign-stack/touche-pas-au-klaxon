<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use Buki\Router\Http\Controller;

final class HomeController extends Controller
{
    public function index(): string
    {
        return View::render('home', ['title' => 'Trajets disponibles']);
    }
}
