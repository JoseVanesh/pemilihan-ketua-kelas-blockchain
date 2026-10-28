<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\Student;
use Illuminate\Contracts\View\View;

class ResultController extends Controller
{
    public function index(): View
    {
        $election = Election::current();
        $candidates = $election
            ? $election->candidates()->withCount('votes')->orderBy('order_no')->get()
            : collect();

        $totalVotes = $election ? $election->votes()->count() : 0;
        $totalStudents = Student::count();
        $turnout = $totalStudents > 0 ? round($totalVotes / $totalStudents * 100, 1) : 0;

        return view('admin.results', compact('election', 'candidates', 'totalVotes', 'totalStudents', 'turnout'));
    }
}
