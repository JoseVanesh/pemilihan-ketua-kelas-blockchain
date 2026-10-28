<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\Student;
use Illuminate\Contracts\View\View;

class ResultController extends Controller
{
    public function index(): View
    {
        $election = Election::current();

        if (! $election) {
            return view('public.results', [
                'election' => null,
                'candidates' => collect(),
                'totalVotes' => 0,
                'totalStudents' => Student::count(),
                'turnout' => 0,
                'leader' => null,
            ]);
        }

        $candidates = $election->candidates()->withCount('votes')->orderBy('order_no')->get();
        $totalVotes = $election->votes()->count();
        $totalStudents = Student::count();
        $turnout = $totalStudents > 0 ? round($totalVotes / $totalStudents * 100, 1) : 0;
        $leader = $candidates->sortByDesc('votes_count')->first();

        return view('public.results', compact('election', 'candidates', 'totalVotes', 'totalStudents', 'turnout', 'leader'));
    }
}
