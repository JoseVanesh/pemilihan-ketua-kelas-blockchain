<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $election = Election::current();
        $query = trim((string) $request->query('q'));

        $students = Student::query()
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $inner->where('name', 'like', "%{$query}%")
                        ->orWhere('nis', 'like', "%{$query}%")
                        ->orWhere('voting_code', 'like', "%{$query}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.students.index', [
            'election' => $election,
            'students' => $students,
            'query' => $query,
            'totalStudents' => Student::count(),
            'totalVoted' => Student::whereNotNull('voted_at')->count(),
        ]);
    }

    public function create(): View
    {
        $election = Election::current();

        return view('admin.students.form', [
            'election' => $election,
            'student' => new Student(['class_name' => $election?->class_name, 'voting_code' => $this->generateCode()]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nis' => ['required', 'string', 'max:30', 'unique:students,nis'],
            'name' => ['required', 'string', 'max:120'],
            'class_name' => ['required', 'string', 'max:100'],
            'voting_code' => ['nullable', 'string', 'max:12', 'unique:students,voting_code'],
        ]);

        $data['voting_code'] = $data['voting_code'] ?: $this->generateCode();

        Student::create($data);

        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function edit(Student $student): View
    {
        return view('admin.students.form', [
            'election' => Election::current(),
            'student' => $student,
        ]);
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $data = $request->validate([
            'nis' => ['required', 'string', 'max:30', Rule::unique('students', 'nis')->ignore($student->id)],
            'name' => ['required', 'string', 'max:120'],
            'class_name' => ['required', 'string', 'max:100'],
            'voting_code' => ['required', 'string', 'max:12', Rule::unique('students', 'voting_code')->ignore($student->id)],
        ]);

        $student->update($data);

        return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        if ($student->votes()->exists()) {
            return back()->with('error', 'Siswa tidak bisa dihapus karena sudah memiliki suara.');
        }

        $student->delete();

        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil dihapus.');
    }

    public function regenerateCode(Student $student): RedirectResponse
    {
        $election = Election::current();
        $alreadyVoted = $election
            ? $student->votes()->where('election_id', $election->id)->exists()
            : false;

        $student->update([
            'voting_code' => $this->generateCode(),
            'voted_at' => $alreadyVoted ? $student->voted_at : null,
        ]);

        return back()->with('success', 'Kode voting baru berhasil dibuat untuk '.$student->name.'.');
    }

    public function codes(): View
    {
        $election = Election::current();

        return view('admin.students.codes', [
            'election' => $election,
            'students' => Student::orderBy('name')->get(),
        ]);
    }

    private function generateCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (Student::query()->where('voting_code', $code)->exists());

        return $code;
    }
}
