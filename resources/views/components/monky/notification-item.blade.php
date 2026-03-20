{{-- resources/views/components/monky/notification-item.blade.php --}}
@props([
    'title'   => '',
    'body'    => '',
    'time'    => '',
    'unread'  => false,
    'color'   => 'primary',  {{-- primary | success | warning | destructive --}}
    'badge'   => null,       {{-- e.g. "MED" --}}
])

<div @class([
    'notification-item',
    'notification-unread' => $unread,
])>
    <div class="flex items-start gap-3">
        <div @class([
            'w-2 h-2 rounded-full mt-2',
            'bg-primary'     => $color === 'primary',
            'bg-success'     => $color === 'success',
            'bg-warning'     => $color === 'warning',
            'bg-destructive' => $color === 'destructive',
        ])></div>
        <div class="flex-1">
            <div class="flex items-center justify-between gap-2 mb-1">
                <h4 @class([
                    'text-sm',
                    'font-semibold' => $unread,
                    'font-medium'   => !$unread,
                ])>{{ $title }}</h4>
                @if($badge)
                    <span class="badge badge-secondary text-xs">{{ $badge }}</span>
                @endif
            </div>
            <p class="text-xs text-muted-foreground">{{ $body }}</p>
            <span class="text-xs text-muted-foreground">{{ $time }}</span>
        </div>
    </div>
</div>
