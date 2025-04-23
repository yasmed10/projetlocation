{{-- resources/views/partials/_flash_messages.blade.php --}}

@if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Succès !</strong> {{ $message }}
        {{-- Bouton fermer pour Bootstrap 5 (adaptez si besoin) --}}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($message = Session::get('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Erreur !</strong> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($message = Session::get('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Attention !</strong> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($message = Session::get('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <strong>Info :</strong> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Affichage groupé des erreurs de validation --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Veuillez corriger les erreurs suivantes :</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif