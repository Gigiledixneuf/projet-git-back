<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Resources\CommentResource;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'content' => 'required|string',
                'article_id' => 'required',
            ]);

            $validated['user_id'] = auth()->user()->id;
            $comment = Comment::create($validated);

            return response()->json([
                "data" => $comment
            ]);


        } catch (\Exception $exception) {

            return response()->json(['error' => 'An error occurred: ' . $exception->getMessage()], 500);
        }
    }


    public function show(string $id)
    {
        // Récupérer les commentaires de l'article avec l'ID spécifié avec pagination
        $comments = Comment::with('user')
            ->where('article_id', $id)->orderBy('created_at', 'desc')
            ->paginate(10);

        // Retourner la collection des commentaires sous forme de CommentResource avec pagination
        return CommentResource::collection($comments);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Récupérer le commentaire à supprimer
            $comment = Comment::findOrFail($id);

            // Vérifier si l'utilisateur connecté est l'auteur du commentaire
            if ($comment->user_id !== auth()->user()->id) {
                return response()->json([
                    'error' => 'You are not authorized to delete this comment.'
                ], 403); // 403 Forbidden
            }

            // Supprimer le commentaire
            $comment->delete();

            return response()->json([
                'message' => 'Comment deleted successfully.'
            ], 200);

        } catch (\Exception $exception) {
            // Gérer les erreurs
            return response()->json([
                'error' => 'An error occurred: ' . $exception->getMessage()
            ], 500);
        }
    }

}