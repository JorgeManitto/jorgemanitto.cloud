{{-- resources/views/components/monky/security-status.blade.php --}}
@props([
    'online' => true,
    'items'  => [],   {{-- [['status'=>'success','label'=>'...','value'=>'...','note'=>'...']] --}}
    'botImage' => null,
])

<div class="card">
    <div class="flex items-center justify-between p-4">
        <div class="flex items-center gap-2.5">
            <span class="bullet bullet-success"></span>
            <span class="font-medium">SECURITY STATUS</span>
        </div>
        <span @class([
            'badge',
            'badge-success' => $online,
            'badge-warning' => !$online,
        ])>
            {{ $online ? 'ONLINE' : 'OFFLINE' }}
        </span>
    </div>
    <div class="bg-accent p-4 relative">
        <div class="grid grid-cols-1 gap-4 max-w-xs">
            @foreach($items as $item)
                <x-monky.security-item
                    :status="$item['status']"
                    :label="$item['label']"
                    :value="$item['value']"
                    :note="$item['note']"
                />
            @endforeach
        </div>
        @if($botImage)
            <div class="absolute top-0 right-0 w-full h-full pointer-events-none">
                <img src="{{ $botImage }}" alt="Security Bot" class="w-full h-full object-contain">
            </div>
        @endif
    </div>
</div>
