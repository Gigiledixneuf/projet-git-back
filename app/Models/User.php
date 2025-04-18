<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


/**
 * @OA\Schema(
 *     schema="UserResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", format="int64", description="Identifiant unique de l'utilisateur"),
 *     @OA\Property(property="name", type="string", description="Nom de l'utilisateur"),
 *     @OA\Property(property="email", type="string", format="email", description="Adresse email de l'utilisateur"),
 *     @OA\Property(property="password", type="string", description="Mot de passe de l'utilisateur", readOnly=true),
 *  
 * )
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
