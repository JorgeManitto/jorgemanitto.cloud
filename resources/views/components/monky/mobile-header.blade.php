{{-- resources/views/components/monky/mobile-header.blade.php --}}
<div class="mobile-only sticky top-0 z-50 mobile-header">
    <div class="flex items-center justify-between px-4 py-3">
        <button class="p-2" aria-label="Menu">☰</button>
        <div class="flex items-center gap-3">
            <div class="h-8 w-16 bg-primary rounded flex items-center justify-center">
                <span class="text-primary-foreground">🐵</span>
            </div>
        </div>
        <button class="relative p-2 bg-secondary rounded" aria-label="Notifications">
            <span class="absolute -top-1 -left-2 bg-primary text-primary-foreground text-xs rounded-full w-5 h-5 flex items-center justify-center">
                {{ $unreadCount ?? 2 }}
            </span>
            🔔
        </button>
    </div>
</div>
