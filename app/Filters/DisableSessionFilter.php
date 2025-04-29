<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class DisableSessionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        session_write_close();
        ini_set('session.use_cookies', 0);
        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing after
        return $response;
    }
} 