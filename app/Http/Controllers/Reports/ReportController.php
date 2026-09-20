<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('reports.index', ['reports' => ReportService::REPORTS]);
    }

    public function show(string $report, Request $request, ReportService $reportService): View
    {
        abort_unless(array_key_exists($report, ReportService::REPORTS), 404);

        return view('reports.show', $reportService->report($report, $request));
    }
}
