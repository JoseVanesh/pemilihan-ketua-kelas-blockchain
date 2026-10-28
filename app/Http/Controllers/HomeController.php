<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\Student;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $election = Election::current();
        $candidates = $election
            ? $election->candidates()->withCount('votes')->orderBy('order_no')->get()
            : collect();

        return view('public.home', [
            'election' => $election,
            'candidates' => $candidates,
            'totalVotes' => $election ? $election->votes()->count() : 0,
            'totalStudents' => Student::count(),
        ]);
    }
}
