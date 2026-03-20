@props([
    'href'    => '#',
    'label'   => '',
    'active'  => false,
    'locked'  => false,
])

@php
    $baseClasses = 'flex items-center gap-2.5 px-3 py-2.5 rounded text-sm transition-colors';

    if ($locked) {
        $stateClasses = 'opacity-40 cursor-not-allowed pointer-events-none';
    } elseif ($active) {
        $stateClasses = 'bg-primary text-primary-foreground font-medium';
    } else {
        $stateClasses = 'text-foreground hover:bg-accent';
    }
@endphp

<a href="{{ $locked ? '#' : $href }}" class="{{ $baseClasses }} {{ $stateClasses }}" @if($locked) aria-disabled="true" tabindex="-1" @endif>
    {{-- Icon slot --}}
    <span class="flex-shrink-0 flex items-center justify-center w-4 h-4">
        {{ $icon ?? '' }}
    </span>

    <span class="truncate">{{ $label }}</span>

    @if($locked)
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-auto flex-shrink-0 opacity-50">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>
    @endif
</a>