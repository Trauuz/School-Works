<?php

namespace App\Http\Controllers;

use App\Http\Requests\CatalogRequest;
use App\Models\MembershipLevel;
use App\Services\MembershipManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LevelController extends Controller
{
    public function index(): View
    {
        return view('catalog.index', ['items' => MembershipLevel::orderBy('name')->get(), 'kind' => 'levels']);
    }

    public function create(): View
    {
        return view('catalog.form', ['item' => new MembershipLevel, 'kind' => 'levels']);
    }

    public function store(CatalogRequest $request): RedirectResponse
    {
        MembershipLevel::create([...$request->validated(), 'fee' => MembershipManager::cents((string) $request->input('fee'))]);

        return redirect()->route('levels.index')->with('success', 'Entry created.');
    }

    public function edit(MembershipLevel $level): View
    {
        return view('catalog.form', ['item' => $level, 'kind' => 'levels']);
    }

    public function show(MembershipLevel $level): View
    {
        return view('catalog.show', ['item' => $level, 'kind' => 'levels']);
    }

    public function update(CatalogRequest $request, MembershipLevel $level): RedirectResponse
    {
        $level->update([...$request->validated(), 'fee' => MembershipManager::cents((string) $request->input('fee'))]);

        return redirect()->route('levels.index')->with('success', 'Entry updated.');
    }

    public function destroy(MembershipLevel $level): RedirectResponse
    {
        if ($level->members()->exists() || $level->transactions()->exists()) {
            throw ValidationException::withMessages(['name' => 'This entry is referenced and cannot be deleted.']);
        }

        $level->delete();

        return redirect()->route('levels.index')->with('success', 'Entry deleted.');
    }
}
