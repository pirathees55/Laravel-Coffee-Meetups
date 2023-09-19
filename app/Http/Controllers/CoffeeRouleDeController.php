<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pair;
use App\Notifications\MatchedPairNotification;
use Illuminate\Support\Facades\Auth;

class CoffeeRouleDeController extends Controller
{
    public function index()
    {
        return view('coffee-roulede.index');
    }

    public function pair()
    {
        $currentUser = Auth::user();

        $potentialPairs = User::where('organization', '!=', $currentUser->organization)
            ->where('id', '!=', $currentUser->id)
            ->whereDoesntHave('pairs', function ($query) use ($currentUser) {
                $query->where('user_id_1', $currentUser->id)
                    ->orWhere('user_id_2', $currentUser->id);
            })
            ->inRandomOrder()
            ->get();

        if ($potentialPairs->isEmpty()) {
            return redirect()->back()->with('error', 'No available pairs found.');
        }

        $randomPair = $potentialPairs->random();

        $pair = new Pair();
        $pair->user_id_1 = $currentUser->id;
        $pair->user_id_2 = $randomPair->id;
        $pair->paired_at = now();
        $pair->save();

        $randomPair->notify(new MatchedPairNotification(['pair' => $pair]));

        return redirect()->back()->with('success', 'You have been paired with ' . $randomPair->name);
    }

    public function matches()
    {
        $currentUser = Auth::user();

        $matches = Pair::where('user_id_1', $currentUser->id)
            ->orWhere('user_id_2', $currentUser->id)
            ->orderBy('paired_at', 'desc')
            ->get();

        return view('coffee-roulede.matches', compact('matches'));
    }
}
