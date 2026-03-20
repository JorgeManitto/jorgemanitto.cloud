{{-- resources/views/components/monky/rebel-rank-item.blade.php --}}
@props([
    'rank'      => 1,
    'name'      => '',
    'handle'    => '',
    'avatar'    => '',
    'points'    => 0,
    'streak'    => null,  {{-- e.g. "2 WEEKS STREAK 🔥" --}}
    'featured'  => false,
])

<div class="flex items-center gap-2">
    {{-- Position --}}
    <div @class([
        'rounded flex items-center justify-center px-2 font-bold',
        'h-10 bg-primary text-primary-foreground' => $featured,
        'h-8 bg-secondary text-secondary-foreground' => !$featured,
    ])>
        {{ $rank }}
    </div>

    {{-- Avatar --}}
    <div @class([
        'rounded-lg overflow-hidden bg-muted',
        'size-16' => $featured,
        'size-12' => !$featured,
    ])>
        <img src="{{ $avatar }}" alt="{{ $name }}" class="w-full h-full object-cover">
    </div>

    {{-- Info --}}
    @if($featured)
        <div class="flex-1 bg-accent rounded p-2">
            <div class="flex items-center justify-between">
                <div>
                    <span class="font-display text-2xl">{{ $name }}</span>
                    <span class="text-muted-foreground text-sm ml-2">{{ $handle }}</span>
                </div>
                <span class="badge badge-default">{{ $points }} POINTS</span>
            </div>
            @if($streak)
                <div class="text-sm text-muted-foreground italic">{{ $streak }}</div>
            @endif
        </div>
    @else
        <div class="flex-1 flex items-center justify-between p-2">
            <div>
                <span class="font-display text-xl">{{ $name }}</span>
                <span class="text-muted-foreground text-sm ml-2">{{ $handle }}</span>
            </div>
            <span class="badge badge-secondary">{{ $points }} POINTS</span>
        </div>
    @endif
</div>
