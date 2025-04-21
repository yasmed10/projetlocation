document.addEventListener('DOMContentLoaded', () => {
    const authLinks = document.getElementById('auth-links');
    const userLinks = document.getElementById('user-links');
    const adminLink = document.getElementById('admin-link');
    const profileLink = document.getElementById('profile-link');
    const logoutLink = document.getElementById('logout-link');

    // --- Simulation ---
    // Pour tester, vous pouvez mettre ceci dans la console du navigateur :
    // localStorage.setItem('userSession', JSON.stringify({ loggedIn: true, role: 'client', name: 'Test Client' }));
    // localStorage.setItem('userSession', JSON.stringify({ loggedIn: true, role: 'partenaire', name: 'Test Partenaire' }));
    // localStorage.setItem('userSession', JSON.stringify({ loggedIn: true, role: 'admin', name: 'Admin User' }));
    // Pour déconnecter : localStorage.removeItem('userSession');
    // Puis rafraîchir la page.

    const userSession = JSON.parse(localStorage.getItem('userSession'));

    if (userSession && userSession.loggedIn) {
        // Utilisateur connecté
        authLinks.style.display = 'none';
        userLinks.style.display = 'flex'; // ou 'block' selon le style

        // Définir le lien du profil en fonction du rôle
        if (userSession.role === 'client') {
            profileLink.textContent = `Profil (${userSession.name})`;
            profileLink.href = '/pages/client/profile.html'; // Ajuster les chemins si nécessaire
             adminLink.style.display = 'none';
        } else if (userSession.role === 'partenaire') {
            profileLink.textContent = `Profil (${userSession.name})`;
            profileLink.href = '/pages/partenaire/profile.html'; // Ajuster les chemins
             adminLink.style.display = 'none';
        } else if (userSession.role === 'admin') {
            profileLink.textContent = `Profil (${userSession.name})`;
            profileLink.href = '#'; // L'admin n'a peut-être pas de page profil standard
            userLinks.style.display = 'none'; // Ou garder pour le nom/déconnexion
            adminLink.style.display = 'block'; // Afficher le lien admin
            adminLink.href = '/pages/admin/dashboard.html'; // Ajuster les chemins
        }

        // Logique de déconnexion
        if(logoutLink) {
            logoutLink.addEventListener('click', (e) => {
                e.preventDefault();
                localStorage.removeItem('userSession');
                // Rediriger vers la page d'accueil ou de connexion
                window.location.href = '/index.html'; // Ajuster le chemin
            });
        }

    } else {
        // Utilisateur non connecté
        authLinks.style.display = 'flex'; // ou 'block'
        userLinks.style.display = 'none';
        adminLink.style.display = 'none';
    }

    // --- Logique de redirection basée sur le rôle (Exemple) ---
    // À placer sur les pages qui nécessitent un rôle spécifique
    // Exemple pour une page réservée aux partenaires:
    /*
    const currentPagePath = window.location.pathname;
    if (currentPagePath.includes('/pages/partenaire/') && (!userSession || userSession.role !== 'partenaire')) {
         console.warn('Accès non autorisé, redirection...');
         // window.location.href = '/login.html?redirect=' + encodeURIComponent(currentPagePath); // Rediriger vers login
         // Pour la démo statique, on peut juste afficher un message ou cacher le contenu
         document.body.innerHTML = '<h1>Accès réservé aux partenaires</h1><a href="/login.html">Se connecter</a>';
    }
    // Idem pour client et admin sur leurs pages respectives
    */

    // --- Logique de redirection après connexion (dans auth.js) ---
    // Après une connexion réussie (simulée), rediriger en fonction du rôle
    // if (role === 'client') window.location.href = '/pages/client/profile.html';
    // else if (role === 'partenaire') window.location.href = '/pages/partenaire/dashboard.html';
    // else if (role === 'admin') window.location.href = '/pages/admin/dashboard.html';
    // else window.location.href = '/index.html'; // Cas par défaut
});