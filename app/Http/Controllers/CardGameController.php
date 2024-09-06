<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GameSession;
use Illuminate\Support\Facades\Auth;

class CardGameController extends Controller
{
    private $cardValues = [
        '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9, '10' => 10, 'J' => 11, 'Q' => 12, 'K' => 13, 'A' => 14
    ];

    private $cardSymbols = ['♠', '♥', '♦', '♣'];

    public function index()
    {
        $user = Auth::user();
        $gameSession = $user->gameSessions()->latest()->first();

        if (!$gameSession || ($gameSession->game_state['gameOver'] && $gameSession->game_state['lastGuess']['correct'])) {
             $gameSession = $user->gameSessions()->create([
                'game_state' => $this->initializeGame(),
            ]);
        }

        // Fetch total wins and losses
        $totalWins = $user->gameSessions->sum('wins');
        $totalLosses = $user->gameSessions->sum('losses');

        return view('cardgame', compact('gameSession', 'totalWins', 'totalLosses'));
    }

    public function handleGuess(Request $request)
    {
        $user = Auth::user();
        $gameSession = $user->gameSessions()->latest()->first();
        $gameState = $gameSession->game_state;

        $guess = $request->input('guess');
        $currentCardValue = $this->cardValues[$gameState['currentCard']['value']];
        $nextCardValue = $this->cardValues[$gameState['nextCard']['value']];

        $correct = false;
        if (($guess === 'higher' && $nextCardValue > $currentCardValue) ||
            ($guess === 'lower' && $nextCardValue < $currentCardValue)) {
            $correct = true;
        }

        $gameState['lastGuess'] = [
            'current' => $gameState['currentCard'],
            'next' => $gameState['nextCard'],
            'guess' => $guess,
            'correct' => $correct,
        ];

        if ($correct) {
            $gameState['score']++;
            if ($gameState['score'] === 5) { // Adjust winning score if needed
                $gameState['gameOver'] = true;
                $gameState['message'] = 'Congratulations! You won!';
                $gameSession->increment('wins');
            } else {
                $gameState['message'] = 'Correct! Keep going!';
                $gameState['currentCard'] = $gameState['nextCard'];
                $gameState['nextCard'] = array_pop($gameState['deck']);
                $gameState['guessedCards'][] = $gameState['currentCard'];
            }
        } else {
            $gameState['gameOver'] = true;
            $gameState['message'] = 'Wrong!';
            $gameSession->increment('losses');
        }

        $gameSession->game_state = $gameState;
        $gameSession->save();

        return redirect()->route('cardgame.index');
    }

    public function newGame()
    {
        $user = Auth::user();
        $user->gameSessions()->create([
            'game_state' => $this->initializeGame(),
        ]);

        return redirect()->route('cardgame.index');
    }

    public function clearSession()
    {
        $user = Auth::user();
        $user->gameSessions()->delete();

        return redirect()->route('cardgame.index');
    }

    private function createDeck()
    {
        $deck = [];
        foreach ($this->cardValues as $value => $rank) {
            foreach ($this->cardSymbols as $cardSymbol) {
                $deck[] = ['value' => $value, 'cardSymbol' => $cardSymbol];
            }
        }
        shuffle($deck);
        return $deck;
    }

    private function initializeGame()
    {
        $deck = $this->createDeck();
        $firstCard = array_pop($deck);
        $secondCard = array_pop($deck);
        return [
            'deck' => $deck,
            'currentCard' => $firstCard,
            'nextCard' => $secondCard,
            'guessedCards' => [$firstCard],
            'score' => 0,
            'gameOver' => false,
            'message' => 'Let\'s start',
            'lastGuess' => null,
            'renderCard' => ['App\Http\Controllers\CardGameController', 'renderCard'] 
        ];
    }

    public function renderCard($card)
    {
        $class = in_array($card['cardSymbol'], ['♥', '♦']) ? 'text-danger' : 'text-black';
        return "<span class=\"$class\">{$card['value']}{$card['cardSymbol']}</span>";
    }
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/cardgame'); 
     
    }
}