<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // AFFICHER LA PAGE DE CONNEXION
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // TRAITER LA SOUMISSION DU FORMULAIRE
    public function login(Request $request)
    {
        // 1. On valide les données reçues
        $credentials = $request->validate([
            'emailpro' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. On tente la connexion avec ta table Utilisateur
        if (Auth::attempt($credentials)) {
            // Sécurité : on regénère la session pour éviter les attaques de fixation
            $request->session()->regenerate();

            // Redirection vers ton beau tableau de bord
            return redirect()->intended(route('technique.dashboard'));
        }

        // 3. Si ça échoue, on renvoie à la page avec une erreur
        return back()->withErrors([
            'emailpro' => 'Les identifiants fournis ne correspondent pas à nos dossiers.',
        ])->onlyInput('emailpro');
    }

    // GÉRER LA DÉCONNEXION
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}