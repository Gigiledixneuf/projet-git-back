<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;
//use Str marche egalement mais c'est mieux de faire appel a Str depuis sa source
use Illuminate\Support\Str;

class ArticleController extends Controller
{

    public function index()
    {
        //return ArticleResource::collection(Article::paginate(1));
        return ArticleResource::collection(
            Article::with(['user', 'likes'])
                ->orderBy('created_at', 'desc') // Trier par date de création décroissante
                ->paginate(9)
        );

        // return Article::all();
    }


    public function store(Request $request)
    {

        try {
            $article = Article::create([
                "title" => $request->title,
                "slug" => Str::slug($request->title),
                "photo" => $request->photo,
                "user_id" => auth()->user()->id,
                "content" => $request->content,
            ]);

            $article->categories()->attach($request->categories);
            $article->tags()->attach($request->tags);

            return response()->json(["data" => $article], 201);
        } catch (\Exception $th) {
            return response()->json($th->getMessage(), 500);
        }
    }


    public function show(int $id)
    {
        $article = Article::withCount("likes")->findOrFail($id);
        // Vérifier s'il existe une vue pour cet article
        $vue = $article->vues()->first();

        if ($vue) {
            $vue->increment('nbr_vue'); // Incrémente nbr_vue si une vue existe déjà
        } else {
            $article->vues()->create(['nbr_vue' => 1]); // Crée une nouvelle vue
        }

        return new ArticleResource($article->load('categories', 'vues')); // Charger aussi 'vues'
    }

    public function update(Request $request, Article $article)
    {
        try {
            // Verifier si l'utilisateur connecté est l'auteur de l'article
            if (auth()->user()->id !== $article->user_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            } else {

                // Modifier l'article
                $article->update([
                    "title" => $request->title,
                    "slug" => Str::slug($request->title),
                    "photo" => $request->photo ?? $article->photo, // Garder l'ancienne photo si une nouvelle photo n'est pas fournie
                    "user_id" => auth()->user()->id,
                    "content" => $request->content,
                ]);

                // Detache les anciennes catégories et tags
                if ($request->has('categories')) {
                    $article->categories()->sync($request->categories); // Utilisation de sync pour mettre à jour les catégories
                }

                if ($request->has('tags')) {
                    $article->tags()->sync($request->tags); // Utilisation de sync() pour mettre à jour les tags
                }
            }


            // Retourne l'article mise à jour avec les nouvelles catégories et tags
            return response()->json(["message" => "Modification reussie", "data" => $article->load('categories', 'tags')], 200);
        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        try {
            // Détacher toutes les catégories
            $article->categories()->detach();
            $article->tags()->detach();
            // Supprimer l'article
            $article->delete();

            return response()->json(['message' => 'Article supprimé avec succès'], 200);
        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function getLatestTreeArticles(Article $article)
    {
        try {
            $article = Article::latest()->take(3)->get();
            $getTreeArticles = ArticleResource::collection($article);
            return response()->json([
                'data' => $getTreeArticles,
            ]);
        } catch (\Exception $message) {
            return response()->json(['error' => $message->getMessage()], 500);
        }
    }

    public function getLatestFiveArticles()
{
    $articles = Article::whereHas('categories', function ($query) {
        $query->where('name', 'actualite');
    })->latest()->take(5)->get();

    return response()->json([
        'data' => ArticleResource::collection($articles),
    ]);
}

}