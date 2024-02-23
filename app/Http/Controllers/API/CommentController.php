<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $comments = Comment::all();
        return response()->json([
            'status' => true,
            'message' => 'Comment récupérés avec succès',
            'comments' => $comments
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
                'content' => 'required|min:5|max:200',
                'tags' => 'nullable|max:50',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048'
            ],
        );

        // renvoi d'un ou plusieurs messages d'erreur si champ(s) incorrect(s)
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // sauvegarde utilisateur en bdd
        $comment = Comment::create([
            'content' => $request->content,
            'image' => $request->image,
            'tags' => $request->tags,
            'post_id' => $request->post_id,
            'user_id' => $request->user_id,
        ]);

        return response()->json(
            [
                'status' => true,
                'message' => 'Commentaire créé avec succès',
                'comment' => $comment
            ],
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        //
        return response()->json([
            'status' => true,
            'message' => 'Commentaire récupéré avec succès',
            'comment' => $comment
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        //
        $validator = Validator::make(
            $request->all(),
            [
                'content' => 'required|min:5|max:50',
                'tags' => 'required|max:50',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048'
            ],
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $comment->update([
            'content' => $request->content,
            'tags' => $request->tags,
            'image' => $request->image,
        ]);

        return response()->json(
            [
                'status' => true,
                'message' => 'Commentaire modifié avec succès',
                'comment' => $comment
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        //

        $comment->delete();

        return response()->json(
            [
                'status' => true,
                'message' => 'Commentaire supprimé avec succès',
                'comment' => $comment
            ]
        );
    }
}
