<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    /** @var CLIRequest|IncomingRequest */
    protected $request;

    protected $helpers = ['mm', 'form', 'url'];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    /** 공개 레이아웃 렌더. */
    protected function render(string $view, array $data = [], string $layout = 'layouts/main'): string
    {
        $data['user']  = mm_user();
        $data['title'] = $data['title'] ?? mm_setting('site_name', 'XINDELELA');
        $data['content'] = view($view, $data);
        return view($layout, $data);
    }
}
