{{-- resources/views/components/monky/right-sidebar.blade.php --}}
@props([
    'notifications' => [],
    'unreadCount'   => 0,
])

<div class="space-y-8 py-4 min-h-screen sticky top-0">

    {{-- Clock Widget --}}
    <x-monky.clock-widget
        location="Buenos Aires, Argentina"
        timezone="UTC-3"
        temperature="18°C"
        :bgImage="asset('assets/pc_blueprint.gif')"
    />

    {{-- Notifications --}}
    <div class="card h-full">
        <div class="flex items-center justify-between p-4">
            <div class="flex items-center gap-2.5">
                <span class="badge badge-default">{{ $unreadCount }}</span>
                <span class="text-sm font-medium uppercase">Notifications</span>
            </div>
            <button class="text-sm opacity-50 hover:opacity-100 uppercase">Clear All</button>
        </div>
        <div class="bg-accent p-2 space-y-2">
            @forelse($notifications as $notification)
                <x-monky.notification-item
                    :title="$notification['title']"
                    :body="$notification['body']"
                    :time="$notification['time']"
                    :unread="$notification['unread'] ?? false"
                    :color="$notification['color'] ?? 'primary'"
                    :badge="$notification['badge'] ?? null"
                />
            @empty
                <p class="text-xs text-muted-foreground text-center py-4">No notifications</p>
            @endforelse
        </div>
    </div>

    {{-- Chat placeholder --}}
    <div class="card">
        <div class="p-4">
            <div class="flex items-center gap-2.5 mb-4">
                <span class="bullet"></span>
                <span class="text-sm font-medium uppercase">Chat</span>
            </div>
            <div class="bg-accent p-4 rounded text-center text-muted-foreground">
                Chat interface would be implemented here
            </div>
        </div>
    </div>

</div>
