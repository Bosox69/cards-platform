<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::withCount(['departments', 'orders'])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateClient($request);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $this->storeLogo($request->file('logo'));
        }

        $validated['is_active'] = $request->has('is_active');

        Client::create($validated);

        return redirect()->route('admin.clients.index')
            ->with('success', 'Le client a été créé avec succès.');
    }

    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $this->validateClient($request, $client->id);

        if ($request->hasFile('logo')) {
            if ($client->logo) {
                Storage::delete('public/' . $client->logo);
            }
            $validated['logo'] = $this->storeLogo($request->file('logo'));
        }

        $validated['is_active'] = $request->has('is_active');

        $client->update($validated);

        return redirect()->route('admin.clients.index')
            ->with('success', 'Le client a été mis à jour avec succès.');
    }

    public function destroy(Client $client)
    {
        if ($client->orders()->count() > 0) {
            return redirect()->route('admin.clients.index')
                ->with('error', 'Ce client ne peut pas être supprimé car il a des commandes associées.');
        }

        if ($client->logo) {
            Storage::delete('public/' . $client->logo);
        }

        $client->delete();

        return redirect()->route('admin.clients.index')
            ->with('success', 'Le client a été supprimé avec succès.');
    }

    private function validateClient(Request $request, ?int $ignoreId = null): array
    {
        $emailRule = 'nullable|email|max:255';
        if ($ignoreId) {
            $emailRule .= '|unique:clients,email,' . $ignoreId;
        } else {
            $emailRule .= '|unique:clients,email';
        }

        return $request->validate([
            'name'           => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email'          => $emailRule,
            'phone'          => 'nullable|string|max:50',
            'address'        => 'nullable|string|max:500',
            'city'           => 'nullable|string|max:255',
            'postal_code'    => 'nullable|string|max:20',
            'country'        => 'nullable|string|max:255',
            'logo'           => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'is_active'      => 'boolean',
        ]);
    }

    private function storeLogo($file): string
    {
        $mimeType = $file->getMimeType();
        $allowed = [
            'image/jpeg'    => 'jpg',
            'image/png'     => 'png',
            'image/svg+xml' => 'svg',
        ];
        $extension = $allowed[$mimeType] ?? null;
        if (!$extension) {
            abort(422, 'Type de fichier non autorisé.');
        }

        $filename = 'clients/' . Str::uuid() . '.' . $extension;
        $file->storeAs('public', $filename);
        return $filename;
    }
}
