<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NewsletterController;
use App\Models\User;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route securisée
Route::group(['middleware' => ['auth:sanctum']], function () {
    
});

Route::apiResource('/articles', ArticleController::class);
Route::apiResource('/categories', CategoryController::class);
Route::apiResource('/comments', CommentController::class);
Route::apiResource('/newsletter' , NewsletterController::class);
Route::get('/article_video/{categoryName}', [ArticleController::class, 'video']);

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json([
            'Message erreur :' => 'Email ou mot de passe incorrect'
        ]);
    }

    $token = $user->createToken($request->email)->plainTextToken;
    $user->token =$token;

    return response()->json([
        'token' => $user
    ]);
});

