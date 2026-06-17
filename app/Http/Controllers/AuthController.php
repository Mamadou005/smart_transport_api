<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Inscription d'un nouvel utilisateur (Passager par défaut)
     */
    public function register(Request $request)
    {
        $request->validate([
            'nom'       => 'required|string',
            'prenom'    => 'required|string',
            'email'     => 'required|email|unique:users',
            'telephone' => 'nullable|string',
            'password'  => 'required|min:6',
        ]);

        $user = User::create([
            'nom'       => $request->nom,
            'prenom'    => $request->prenom,
            'email'     => $request->email,
            'telephone' => $request->telephone,
            'password'  => Hash::make($request->password),
            'role'      => 'passager', // Rôle par défaut attribué à l'inscription
        ]);

        // Génération du token de connexion via Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        // Retourne la réponse avec protection contre les caractères UTF-8 mal formés dans Docker
        return response()->json([
            'token' => $token,
            'user'  => $user,
        ], 201, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Connexion de l'utilisateur (Passager, Agent ou Admin)
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Recherche de l'utilisateur par son email
        $user = User::where('email', $request->email)->first();

        // Vérification de l'existence de l'utilisateur et du mot de passe haché
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Identifiants incorrects.'],
            ]);
        }

        // Génération du token de connexion via Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        // Retourne la réponse avec protection contre les caractères UTF-8 mal formés dans Docker
        return response()->json([
            'token' => $token,
            'user'  => $user,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Déconnexion de l'utilisateur (Révocation du token actuel)
     */
    public function logout(Request $request)
    {
        // Supprime le token qui a servi à la requête actuelle
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnecté avec succès'
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Récupération du profil de l'utilisateur connecté
     */
    public function me(Request $request)
    {
        // Retourne les infos de l'utilisateur authentifié par le Token
        return response()->json(
            $request->user(),
            200,
            [],
            JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
        );
    }
}
