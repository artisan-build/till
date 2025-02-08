<?php

declare(strict_types=1);

namespace Packages\till\tests;

use App\Models\Team;
use App\Models\User;
use ArtisanBuild\Till\Enums\TestPlans;
use ArtisanBuild\Till\Events\SubscriptionStarted;
use ArtisanBuild\Till\States\SubscriberState;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Thunk\Verbs\Facades\Verbs;

beforeEach(function () {
    Verbs::commitImmediately();

    // Create a user and team
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->current_team_id = $team->id;
    $user->save();

    Auth::login($user);
});

test('subscription started → it creates a state for the subscription', function () {

    SubscriptionStarted::fire(
        subscriber_id: Auth::user()->current_team_id,
        plan_id: TestPlans::Solo->value
    );

    // Verify state was updated
    $state = SubscriberState::load(Auth::user()->current_team_id);
    expect($state->plan_id)->toBe(TestPlans::Solo->value);
});

test('subscription started → it caches the correct abilities', function () {
    // Create a new state

    // Fire the subscription started event
    SubscriptionStarted::fire(
        subscriber_id: Auth::user()->current_team_id,
        plan_id: TestPlans::Solo->value
    );

    $abilities = Cache::get('subscription-'.Auth::user()->currentTeam->id);
    expect($abilities)->toBeArray()
        ->and(array_key_exists('ArtisanBuild\Till\Plans\Abilities\AddSeats', $abilities))->toBeTrue();
});
