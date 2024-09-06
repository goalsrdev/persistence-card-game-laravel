<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameSession extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'game_state',
        'wins',
        'losses',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'game_state' => 'array', // Ensure the game state is stored and retrieved as an array
        
    ];

    /**
     * Get the user that owns the game session.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}