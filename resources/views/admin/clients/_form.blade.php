@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Veuillez corriger les erreurs suivantes :</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header py-3">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Informations</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Nom du client <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" required
                           value="{{ old('name', $client->name ?? '') }}"
                           class="form-control @error('name') is-invalid @enderror">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="contact_person" class="form-label fw-semibold">Personne de contact</label>
                        <input type="text" id="contact_person" name="contact_person"
                               value="{{ old('contact_person', $client->contact_person ?? '') }}"
                               class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label fw-semibold">Email</label>
                        <input type="email" id="email" name="email"
                               value="{{ old('email', $client->email ?? '') }}"
                               class="form-control @error('email') is-invalid @enderror">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3 mt-3">
                    <label for="phone" class="form-label fw-semibold">Téléphone</label>
                    <input type="text" id="phone" name="phone"
                           value="{{ old('phone', $client->phone ?? '') }}"
                           class="form-control">
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label fw-semibold">Adresse</label>
                    <textarea id="address" name="address" rows="2" class="form-control">{{ old('address', $client->address ?? '') }}</textarea>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="postal_code" class="form-label fw-semibold">Code postal</label>
                        <input type="text" id="postal_code" name="postal_code"
                               value="{{ old('postal_code', $client->postal_code ?? '') }}"
                               class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="city" class="form-label fw-semibold">Ville</label>
                        <input type="text" id="city" name="city"
                               value="{{ old('city', $client->city ?? '') }}"
                               class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="country" class="form-label fw-semibold">Pays</label>
                        <input type="text" id="country" name="country"
                               value="{{ old('country', $client->country ?? 'France') }}"
                               class="form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header py-3">
                <h5 class="mb-0"><i class="fas fa-image me-2 text-primary"></i>Logo & statut</h5>
            </div>
            <div class="card-body">
                @if(isset($client) && $client->logo && Storage::disk('public')->exists($client->logo))
                    <div class="mb-3 text-center">
                        <img src="{{ Storage::url($client->logo) }}" alt="Logo actuel"
                             class="border rounded p-2 bg-light" style="max-height:120px;">
                        <div class="small text-muted mt-1">Logo actuel</div>
                    </div>
                @endif

                <div class="mb-3">
                    <label for="logo" class="form-label fw-semibold">
                        {{ isset($client) && $client->logo ? 'Remplacer le logo' : 'Ajouter un logo' }}
                    </label>
                    <input type="file" id="logo" name="logo" accept="image/jpeg,image/png,image/svg+xml"
                           class="form-control @error('logo') is-invalid @enderror">
                    <div class="form-text">JPG, PNG ou SVG — 2 Mo max.</div>
                    @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-check form-switch mt-4">
                    <input type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                           {{ old('is_active', $client->is_active ?? 1) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="is_active">Client actif</label>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4 d-flex justify-content-end gap-2">
    <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary">Annuler</a>
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save me-2"></i>{{ $submitLabel ?? 'Enregistrer' }}
    </button>
</div>
