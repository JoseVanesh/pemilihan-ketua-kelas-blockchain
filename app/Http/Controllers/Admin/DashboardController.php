<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\Student;
use App\Services\BlockchainService;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly BlockchainService $blockchain) {}

    public function __invoke(): View
    {
        $election = Election::current();
        $totalStudents = Student::count();
        $totalVotes = $election ? $election->votes()->count() : 0;
        $turnout = $totalStudents > 0 ? round($totalVotes / $totalStudents * 100, 1) : 0;

        $candidates = $election
            ? $election->candidates()->withCount('votes')->orderBy('order_no')->get()
            : collect();

        $recentBlocks = $election
            ? $election->blocks()->with('candidate')->orderByDesc('position')->limit(6)->get()
            : collect();

        $chain = $election ? $this->blockchain->verify($election) : null;

        return view('admin.dashboard', compact(
            'election',
            'totalStudents',
            'totalVotes',
            'turnout',
            'candidates',
            'recentBlocks',
            'chain',
        ));
    }
}
