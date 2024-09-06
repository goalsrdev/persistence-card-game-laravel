<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">   
   

    <title>Guess High-Low</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-display {
            width: 16rem;
            height: 24rem;
        }

        .small-card {
            width: 3rem;
            height: 4rem;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100 bg-light">
    <div class="container">
        <div class="row mt-3"> 
            <div class="col-md-10">
                <h3>Welcome, {{ Auth::user()->name }} </h3> 
            </div>
            <div class="col-md-2">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger">Logout</button>
                </form>
            </div>
        </div>
        
            <div class="row mt-5 pb-5 bg-white">
                <div class="col-4 mt-4">
                    <div class="card card-display mb-2 mx-auto">
                        <div class="card-body d-flex flex-column justify-content-center align-items-center">
                            @if (isset($gameSession->game_state['currentCard']))
                                <div class="display-1">
                                    @php 
                                        $controller = app(\App\Http\Controllers\CardGameController::class); 
                                        echo $controller->renderCard($gameSession->game_state['currentCard']); 
                                    @endphp
                                </div>
                            @endif
                        </div>
                        <div class="d-flex mt-4 overflow-auto">
                            @foreach ($gameSession->game_state['guessedCards'] as $card)
                                <div class="small-card border border-secondary d-flex justify-content-center align-items-center me-2 small">
                                    @php 
                                        echo $controller->renderCard($card); 
                                    @endphp
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-md-8 p-5">
                    <h3>Guess Next Card !!</h3>

                    <div class="px-8">
                        <div class="mb-4 text-secondary my-4">
                            Total Wins: {{ $totalWins }} | Total Losses: {{ $totalLosses }}
                        </div>
                        <div class="mb-4 text-secondary">Score: {{ $gameSession->game_state['score'] }}</div>

                        @if ($gameSession->game_state['message'])
                            <div class="alert alert-info mb-4">
                                {{ $gameSession->game_state['message'] }}
                            </div>
                        @endif
                                @if ($gameSession->game_state['gameOver'])
                                    @if (!$gameSession->game_state['lastGuess']['correct']) 
                                        <div class="alert alert-danger mb-4 mt-2">
                                            Sorry, you lost!
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('cardgame.new') }}">
                                        @csrf
                                        <button type="submit" class="btn btn-success mb-3">
                                            New Game
                                        </button>
                                    </form>
                                @endif 
                        @if (isset($gameSession->game_state['lastGuess']))
                            <div class="mb-4">
                                @if (isset($gameSession->game_state['lastGuess']['current']))
                                    <span class="h4 me-2">
                                        @php 
                                            echo $controller->renderCard($gameSession->game_state['lastGuess']['current']); 
                                        @endphp
                                    </span>
                                @endif
                                <span class="mx-2">{{ $gameSession->game_state['lastGuess']['guess'] ?? '' }}</span>
                                @if (isset($gameSession->game_state['lastGuess']['next']))
                                    <span class="h4 ms-2">
                                        @php 
                                            echo $controller->renderCard($gameSession->game_state['lastGuess']['next']); 
                                        @endphp
                                    </span>
                                @endif
                                @if(isset($gameSession->game_state['lastGuess']['correct']))
                                    @if($gameSession->game_state['lastGuess']['correct'])
                                        <span class="badge bg-success ms-2">Correct</span>
                                    @else
                                        <span class="badge bg-danger ms-2">Incorrect</span>
                                    @endif
                                @endif
                                
                            </div>
                        @endif
                        
                        <form method="POST" action="{{ route('cardgame.guess') }}" class="mb-4">
                            @csrf
                            <input type="hidden" name="action" value="guess">
                            <div class="btn-group" role="group">
                                <button type="submit" name="guess" value="lower" class="btn btn-outline-primary">
                                    ↓ Lower
                                </button>
                                <button type="submit" name="guess" value="higher" class="btn btn-primary">
                                    ↑ Higher
                                </button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('cardgame.clear') }}">
                            @csrf
                            <button type="submit" class="btn btn-secondary">
                                Clear Session
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>