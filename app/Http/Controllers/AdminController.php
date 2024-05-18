<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Exception;

abstract class AdminController extends Controller
{

    protected $content = [];

    public function __construct()
    {
    }

}
