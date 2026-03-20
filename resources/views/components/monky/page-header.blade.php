{{-- resources/views/components/monky/page-header.blade.php --}}
@props([
    'icon'        => '⟨⟩',
    'title'       => 'Overview',
    'lastUpdated' => null,
])

<div class="flex items-center gap-4 px-4 lg:px-6 py-3 lg:pt-7 bg-background border-2 border-border sticky top-0 lg:top-0 z-10">
    <div class="size-7 lg:size-9 bg-primary rounded flex items-center justify-center">
        <span class="text-primary-foreground">{{ $icon }}</span>
    </div>
    <h1 class="text-xl lg:text-4xl font-display">{{ $title }}</h1>
    @if($lastUpdated)
        <span class="ml-auto text-xs lg:text-sm text-muted-foreground">Last updated {{ $lastUpdated }}</span>
    @endif
</div>
