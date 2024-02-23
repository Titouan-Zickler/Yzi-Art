<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $users = User::all();
        return response()->json([
            'status' => true,
            'message' => 'Utilisateurs récupérés avec succès',
            'users' => $users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make(
            $request->all(),
            [
                'pseudo' => 'required|min:5|max:20|unique:users',
                'email' => 'required|email|max:50|unique:users',
                'password' => [
                    'required', 'confirmed',
                    Password::min(8) // minimum 8 caractères   
                        ->mixedCase() // au moins 1 minuscule et une majuscule
                        ->letters()  // au moins une lettre
                        ->numbers() // au moins un chiffre
                        ->symbols() // au moins un caractère spécial parmi ! @ # $ % ^ & * ?  
                ],
                'image' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048'
            ],
        );

        // renvoi d'un ou plusieurs messages d'erreur si champ(s) incorrect(s)
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // sauvegarde utilisateur en bdd
        $user = User::create([
            'pseudo' => $request->pseudo,
            'image' => $request->image,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(
            [
                'status' => true,
                'message' => 'Utilisateur créé avec succès',
                'user' => $user
            ],
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
        return response()->json([
            'status' => true,
            'message' => 'Utilisateur récupéré avec succès',
            'user' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //

        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email|max:50|unique:users',
                'oldPassword' => 'nullable',
                'password' => [
                    'nullable', 'confirmed',
                    Password::min(8) // minimum 8 caractères   
                        ->mixedCase() // au moins 1 minuscule et une majuscule
                        ->letters()  // au moins une lettre
                        ->numbers() // au moins un chiffre
                        ->symbols() // au moins un caractère spécial parmi ! @ # $ % ^ & * ?  
                ],
                'image' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048'
            ],
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // On modifie les informations de l'utilisateur
        $user->update([
            'email' => $request->email,
            'image' => $request->image,
        ]);

        // si nouveau mdp choisi (et qui respecte bien sûr les critères de sécurité du validateur)
        if ($request->password) {

            // si ancien mdp fourni ET valide (vérifié via Hash::check), modification validée 
            if ($request->oldPassword && Hash::check($request->oldPassword, User::find($user->id)->password)) {
                // on sauvegarde le nouveau mot de passe hashé
                $user->update([
                    'password' => Hash::make($request->password)
                ]);

                // sinon => on renvoie une erreur
            } else {
                return response()->json([
                    'message' => 'mot de passe actuel non renseigné ou incorrect',
                ], 400);
            }
        }

        return response()->json(
            [
                'status' => true,
                'message' => 'Utilisateur modifié avec succès',
                'user' => $user
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
        $user->delete();

        return response()->json(
            [
                'status' => true,
                'message' => 'Utilisateur supprimé avec succès',
                'user' => $user
            ]
        );
    }
}
