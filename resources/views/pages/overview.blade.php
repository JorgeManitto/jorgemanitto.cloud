{{-- resources/views/pages/overview.blade.php --}}
@extends('layouts.app')

@section('title', 'Overview')

@section('content')

    {{-- Page Header --}}
    <x-monky.page-header
        icon="⟨⟩"
        title="Overview"
        lastUpdated="12:05"
    />

    {{-- Content body --}}
    <div class="px-3 lg:px-6 py-6 lg:py-10 bg-background border-2 border-border space-y-8 lg:space-y-14">

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <x-monky.stat-card
                label="ISSUES COMPLETED"
                icon="⚙"
                value="49%"
                description="WEEKLY SCOPE"
                trend="up"
            />
            <x-monky.stat-card
                label="MINUTES LOST"
                icon="🖥"
                value="642'"
                description="IN MEETINGS AND RABBIT HOLES"
                trend="down"
            />
            <x-monky.stat-card
                label="ACCIDENTS"
                icon="💥"
                value="0"
                description="THE CLIENT ALWAYS IS RIGHT"
                badge="4 weeks 🔥"
            />
        </div>

        {{-- Chart --}}
        <x-monky.chart-section activeRange="week" />

        {{-- Two Column Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <x-monky.rebels-ranking
                :newCount="2"
                :rebels="[
                    [
                        'name'   => 'KRIMSON',
                        'handle' => '@KRIMSON',
                        'avatar' => asset('avatars/user_krimson.png'),
                        'points' => 148,
                        'streak' => '2 WEEKS STREAK 🔥',
                    ],
                    [
                        'name'   => 'MATI',
                        'handle' => '@MATI',
                        'avatar' => asset('avatars/user_mati.png'),
                        'points' => 129,
                    ],
                    [
                        'name'   => 'PEK',
                        'handle' => '@MATT',
                        'avatar' => asset('avatars/user_pek.png'),
                        'points' => 108,
                    ],
                    [
                        'name'   => 'JOYBOY',
                        'handle' => '@JOYBOY',
                        'avatar' => asset('avatars/user_joyboy.png'),
                        'points' => 64,
                    ],
                ]"
            />

            <x-monky.security-status
                :online="true"
                :botImage="asset('assets/bot_greenprint.gif')"
                :items="[
                    ['status' => 'success', 'label' => 'GUARD BOTS',     'value' => '124/124', 'note' => 'RUNNING...'],
                    ['status' => 'success', 'label' => 'FIREWALL',       'value' => '99.9%',   'note' => 'BLOCKED'],
                    ['status' => 'warning', 'label' => 'HTML WARNINGS',  'value' => '12042',   'note' => 'ACCESSIBILITY'],
                ]"
            />
        </div>

    </div>

@endsection
