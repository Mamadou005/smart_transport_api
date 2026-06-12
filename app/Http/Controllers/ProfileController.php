<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller {

    // Voir son profil
    public function show(Request $request) {
        return response()->json($request->user());
    }

    // Modifier ses infos
    public function update(Request $request) {
        $user = $request->user();

        $request->validate([
            'nom'       => 'sometimes|string|max:100',
            'prenom'    => 'sometimes|string|max:100',
            'telephone' => 'sometimes|string|max:20',
            'email'     => 'sometimes|email|unique:users,email,'.$user->id,
        ]);

        $user->update($request->only([
            'nom', 'prenom', 'telephone', 'email'
        ]));

        return response()->json([
            'message' => 'Profil mis à jour',
            'user'    => $user->fresh(),
        ]);
    }

    // Changer mot de passe
    public function changerMotDePasse(Request $request) {
        $request->validate([
            'ancien_password'   => 'required',
            'nouveau_password'  => 'required|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->ancien_password, $user->password)) {
            throw ValidationException::withMessages([
                'ancien_password' => ['Mot de passe actuel incorrect.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->nouveau_password),
        ]);

        return response()->json([
            'message' => 'Mot de passe modifié avec succès',
        ]);
    }

    // Supprimer son compte
    public function supprimer(Request $request) {
        $request->validate([
            'password' => 'required',
        ]);

        $user = $request->user();

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Mot de passe incorrect.'],
            ]);
        }

        $request->user()->currentAccessToken()->delete();
        $user->delete();

        return response()->json([
            'message' => 'Compte supprimé avec succès',
        ]);
    }
}
