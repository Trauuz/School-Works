<?php

namespace App\Http\Controllers;

use App\Http\Requests\CatalogRequest;
use App\Models\MembershipCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('catalog.index', ['items' => MembershipCategory::orderBy('name')->get(), 'kind' => 'categories']);
    }

    public function create(): View
    {
        return view('catalog.form', ['item' => new MembershipCategory, 'kind' => 'categories']);
    }

    public function store(CatalogRequest $request): RedirectResponse
    {
        MembershipCategory::create($request->validated());

        return redirect()->route('categories.index')->with('success', 'Entry created.');
    }

    public function edit(MembershipCategory $category): View
    {
        return view('catalog.form', ['item' => $category, 'kind' => 'categories']);
    }

    public function show(MembershipCategory $category): View
    {
        return view('catalog.show', ['item' => $category, 'kind' => 'categories']);
    }

    public function update(CatalogRequest $request, MembershipCategory $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()->route('categories.index')->with('success', 'Entry updated.');
    }

    public function destroy(MembershipCategory $category): RedirectResponse
    {
        if ($category->members()->exists() || $category->transactions()->exists()) {
            throw ValidationException::withMessages(['name' => 'This entry is referenced and cannot be deleted.']);
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Entry deleted.');
    }
}
