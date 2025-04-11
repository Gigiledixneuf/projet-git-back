<?php

namespace App\Http\Controllers;

use App\Models\Tags;
use Illuminate\Http\Request;
use App\Http\Resources\ArticleResource;


class TagsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $tags = Tags::all();
            return response()->json($tags);
        } catch (\Exception $th) {
            return response()->json(["Erreur : "=> $th->getMessage()]);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
            ]);

            $tag = Tags::create([
                'name'=> $validated['name'],
                'description' => $request->description
            ]);
            return response()->json($tag);
        } catch (\Exception $th) {
            return response()->json([''=> $th->getMessage()]);
        }

    }


    public function show($id)
    {
        $tag = Tags::find($id);
        if (!$tag) {
            return response()->json([
                'message' => 'Tag not found'
            ], 404);
        }

        $articles = $tag->articles()->with(['user', 'likes'])->get();;

        return ArticleResource::collection($articles);
    }


    public function update(Request $request, Tags $tags)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
            ]);

            $tags ->update([
                'name'=> $validated['name'],
                'description' => $request->description
            ]);

            return response()->json(['tags'=> $tags]);
        } catch (\Exception $th) {
            return response()->json(['Erreur : '=> $th->getMessage()]);
        }

    }


    public function destroy(Tags $tags)
    {
        try {
            $tags->delete();
            return response()->json([
                'tags'=> $tags
            ]);
        } catch (\Exception $th) {
            return response()->json(['Erreur : '=> $th->getMessage()]);
        }
    }
}
