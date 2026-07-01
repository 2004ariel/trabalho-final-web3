<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        if (session()->get('usuario')) {
            return redirect()->to('/produtos');
        }

        return redirect()->to('/login');
    }
}
