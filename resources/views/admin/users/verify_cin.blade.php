@extends('layouts.admin')

@section('title', 'VÃ©rification CIN | Admin')

@section('styles')
<style> /* Styles existants (raccourci) */
    .cin-verification-container { max-width: 800px; margin: auto; }
    .user-details { margin-bottom: 20px; /*...*/ }
    .cin-images { display: flex; gap: 20px; /*...*/ }
    .cin-images img { max-width: 100%; /*...*/ }
    .cin-image-container { flex: 1; /*...*/ }
    .cin-actions { margin-top: 30px; display: flex; justify-content: space-between; }
    .btn-approve { /*...*/ }
    .btn-reject { /*...*/ } /* Le formulaire de rejet n'a plus de textarea */
</style>
@endsection

@section('content')
<div class="admin-content-wrapper cin-verification-container">
    <section class="admin-section">
        <h1>VÃ©rification de la CIN</h1>
        @include('partials._flash_messages')

        <div class="user-details">
            <h3>Utilisateur : {{ $user->full_name }}</h3>
            <p>Email : {{ $user->email }}</p>
            <p>RÃ´le actuel : {{ ucfirst($user->role) }}</p>
            {{-- !! Affichage Raison Rejet PrÃ©cÃ©dent RetirÃ© !! --}}
        </div>

        <div class="cin-images">
            <div class="cin-image-container"><h4>CIN Recto</h4> @if($user->cin_recto_url) <a href="{{ $user->cin_recto_url }}" target="_blank"><img src="{{ $user->cin_recto_url }}" alt="CIN Recto"></a> @else <p>N/A</p> @endif </div>
            <div class="cin-image-container"><h4>CIN Verso</h4> @if($user->cin_verso_url) <a href="{{ $user->cin_verso_url }}" target="_blank"><img src="{{ $user->cin_verso_url }}" alt="CIN Verso"></a> @else <p>N/A</p> @endif </div>
        </div>

        <div class="cin-actions">
             {{-- Formulaire de Rejet (SimplifiÃ©) --}}
             <form action="{{ route('admin.users.reject_cin', $user->id) }}" method="POST" onsubmit="return confirm('Rejeter cette CIN ? Les fichiers seront supprimÃ©s.')">
                 @csrf
                 @method('PATCH') {{-- Utiliser PATCH car c'est une mise Ã  jour (suppression des chemins) --}}
                 {{-- !! Textarea pour la raison retirÃ©e !! --}}
                 <button type="submit" class="btn btn-danger btn-reject">Rejeter CIN (Suppr. fichiers)</button>
             </form>

             {{-- Formulaire d'Approbation (InchangÃ©) --}}
            <form action="{{ route('admin.users.approve_cin', $user->id) }}" method="POST" onsubmit="return confirm('Approuver cette CIN (change le rÃ´le en PropriÃ©taire) ?')">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-success btn-approve">Approuver CIN</button>
            </form>
        </div>
    </section>
</div>
@endsection