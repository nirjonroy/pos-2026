<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, ReportService $reportService): View
    {
        return view('dashboard.index', $reportService->dashboard($request->only(['branch_id', 'date'])));
    }
}
