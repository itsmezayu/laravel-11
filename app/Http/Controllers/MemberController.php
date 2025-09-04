<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $sortableColumns = ['name', 'position', 'created_at'];
        $sortBy = (string) $request->query('sort_by', 'created_at');
        $sortDirection = (string) $request->query('sort_direction', 'desc');
        if (!in_array($sortBy, $sortableColumns)) $sortBy = 'created_at';
        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) $sortDirection = 'desc';

        $membersQuery = Member::query();

        if (Gate::denies('is-admin')) {
            $membersQuery->where('user_id', Auth::id());
        }

        $members = $membersQuery->orderBy($sortBy, $sortDirection)->paginate(10);
        return view('members.index', compact('members', 'sortBy', 'sortDirection'));
    }

    public function create(): View
    {
        return view('members.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->members()->create($validatedData);

        return redirect()->route('members.index')->with('success', 'Member baru berhasil ditambahkan.');
    }

    public function show(Member $member)
    { /* Tidak digunakan */
    }

    public function edit(Member $member): View
    {
        if (Auth::id() !== $member->user_id && Gate::denies('is-admin')) {
            abort(403, 'THIS ACTION IS UNAUTHORIZED.');
        }
        return view('members.edit', compact('member'));
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        if (Auth::id() !== $member->user_id && Gate::denies('is-admin')) {
            abort(403, 'THIS ACTION IS UNAUTHORIZED.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
        ]);

        $member->update($validatedData);
        return redirect()->route('members.index')->with('success', 'Data member berhasil diperbarui.');
    }

    public function destroy(Member $member): RedirectResponse
    {
        if (Auth::id() !== $member->user_id && Gate::denies('is-admin')) {
            abort(403, 'THIS ACTION IS UNAUTHORIZED.');
        }

        $member->delete();
        return redirect()->route('members.index')->with('success', 'Member berhasil dihapus.');
    }
}
