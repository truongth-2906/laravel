<?php

namespace App\Domains\Portfolio\Http\Controllers;

use App\Domains\Portfolio\Services\PortfolioService;
use App\Http\Controllers\Controller;

class PortfolioController extends Controller
{
    /** @var PortfolioService */
    protected $portfolioService;

    /**
     * constructor function
     *
     * @param PortfolioService $portfolioService
     */
    public function __construct(PortfolioService $portfolioService)
    {
        $this->portfolioService = $portfolioService;
    }

    /**
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download($filename)
    {
        return $this->portfolioService->findAndDownload($filename);
    }
}
