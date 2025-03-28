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


    public function video(string $article )
    {
     
        $articles = Article::whereHas('categories', function($query) use ($article) {
            $query->where('name', $article);
        })->get();

        return response()->json($articles);
    }
    public function index()
    {
        //return ArticleResource::collection(Article::paginate(1));
        return ArticleResource::collection(Article::paginate(10));
        // return Article::all();
    }


    public function store(ArticleRequest $request)
    {

        try {
            $article = Article::create([
                "title" => $request->title,
                "slug" => Str::slug($request->title),
                "photo" => $request->photo,
                "auteur" => $request->auteur,
                "content" => $request->content,
            ]);

            $article->categories()->attach($request->categories);

            return response()->json($article->load('categories'), 201);
        } catch (\Exception $th) {
            return response()->json($th->getMessage(),500);
        }


    }


    public function show( Article $article)
    {
        return new ArticleResource($article->load('categories'));
    }

    public function update(ArticleRequest $request, Article $article)
    {
        try {
            $article->update([
                "title" => $request->title,
                "slug" => Str::slug($request->title),
                "photo" => $request->photo,
                "auteur" => $request->auteur,
                "content" => $request->content,
            ]);

            $article->categories()->attach($request->category_id);

            return response()->json(new ArticleResource($article->load('categories')));
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
            // Supprimer l'article
            $article->delete();

            return response()->json(['message' => 'Article supprimé avec succès'], 200);
        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
}
