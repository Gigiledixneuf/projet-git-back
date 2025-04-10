<?php

use App\Http\Middleware\RejectUrlParameters;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ArticleController,
    ArticleLikeController,
    AuthController,
    CategoryController,
    CommentController,
    NewsletterController,
    TagsController,
    VueController
};
use Illuminate\Http\Request;






Route::middleware([RejectUrlParameters::class])->group(function () {
    // Route pour récupérer l'utilisateur connecté
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    // ------------------- ROUTES PUBLIQUES ------------------- //

    // Page d’accueil ou liste des articles
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/getlatesttreearticles', [ArticleController::class, 'getLatestTreeArticles']);
    Route::get('/getlatestfivearticles', [ArticleController::class, 'getLatestFiveArticles']);


    // Affichage d’un article individuel
    Route::get('/articles/{article}', [ArticleController::class, 'show']);
    Route::get('/comments/{article}', [CommentController::class, 'show']);

    // Login and Register
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    // Categories, Newsletter, tags, vues : accessibles publiquement
    Route::apiResource('/newsletters', NewsletterController::class);
    Route::apiResource('/tags', TagsController::class);
    Route::apiResource('/vues', VueController::class);
    Route::apiResource('/categories', CategoryController::class);

    // ------------------- ROUTES PROTÉGÉES ------------------- //
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        // Like d'un article (protégé)
        Route::post('/articles/{articleId}/likes', ArticleLikeController::class);

        // Les autres routes (store, update, destroy, etc.) sont protégées
        Route::apiResource('/articles', ArticleController::class)->except(['index', 'show']);
        Route::apiResource('/comments', CommentController::class)->except(['show']);
    });



});
