<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class AccountController extends Controller
{
    public function index(): InertiaResponse
    {
        $accounts = Account::all();
        
        return Inertia::render('Accounts/Index', compact('accounts'));
    }

    public function show(Account $account): InertiaResponse
    {
        // could transform account and contacts to a DTO or Resource Class to not expose unneccessary data to the Client
        $account->load(['contacts', 'owner' => function ($query) {
                $query->select('id', 'name');
            }
        ]);

        return Inertia::render('Accounts/Show', compact('account'));
    }

    public function create(): InertiaResponse
    {
        $owners = User::select('id', 'name')->get();
        
        return Inertia::render('Accounts/Create', compact('owners'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'town_city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'post_code' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'owner_id' => ['required', Rule::exists('users', 'id')]
        ]);

        Account::create($validated);

        return redirect()->route('accounts.index');
    }

    public function edit(Account $account)
    {
        $owners = User::select('id', 'name')->get();

        return Inertia::render('Accounts/Edit', compact('account', 'owners'));
    }

    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'town_city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'post_code' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'owner_id' => ['required', Rule::exists('users', 'id')]
        ]);

        $account->update($validated);

        return redirect()->route('accounts.show', $account);
    }

    public function destroy(Account $account)
    {
        $account->delete();

        return redirect()->route('accounts.index');
    }
}