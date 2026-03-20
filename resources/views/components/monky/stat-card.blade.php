{{-- resources/views/components/monky/stat-card.blade.php --}}
@props([
    'label'       => '',
    'icon'        => '⚙',
    'value'       => '0',
    'description' => '',
    'trend'       => null,   {{-- 'up' | 'down' | null --}}
    'badge'       => null,   {{-- texto del badge o null --}}
])

<div class="card">
    {{-- Header --}}
    <div class="flex items-center justify-between p-4">
        <div class="flex items-center gap-2.5">
            <span class="bullet"></span>
            <span class="font-medium">{{ $label }}</span>
        </div>
        <span class="text-muted-foreground">{{ $icon }}</span>
    </div>

    {{-- Body --}}
    <div class="bg-accent p-4 relative overflow-hidden">
        <div class="flex items-center">
            <span class="text-4xl lg:text-5xl font-display">{{ $value }}</span>
            @if($badge)
                <span class="badge badge-default ml-3">{{ $badge }}</span>
            @endif
        </div>
        <p class="text-xs lg:text-sm text-muted-foreground mt-2">{{ $description }}</p>

        {{-- Trend arrow marquee --}}
        @if($trend === 'up')
            <div class="absolute top-0 right-0 w-14 h-full pointer-events-none overflow-hidden">
                <div class="flex flex-col text-success animate-marquee-up">
                    <span class="text-5xl font-display">↑</span>
                    <span class="text-5xl font-display">↑</span>
                    <span class="text-5xl font-display">↑</span>
                </div>
            </div>
        @elseif($trend === 'down')
            <div class="absolute top-0 right-0 w-14 h-full pointer-events-none overflow-hidden">
                <div class="flex flex-col text-destructive animate-marquee-down">
                    <span class="text-5xl font-display">↓</span>
                    <span class="text-5xl font-display">↓</span>
                    <span class="text-5xl font-display">↓</span>
                </div>
            </div>
        @endif
    </div>
</div>
