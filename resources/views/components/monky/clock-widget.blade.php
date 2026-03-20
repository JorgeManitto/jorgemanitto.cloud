{{-- resources/views/components/monky/clock-widget.blade.php --}}
@props([
    'location'    => 'Buenos Aires, Argentina',
    'timezone'    => 'UTC-3',
    'temperature' => '18°C',
    'bgImage'     => null,
])

<div class="card aspect-[2] relative overflow-hidden">
    <div class="bg-accent/30 p-4 h-full flex flex-col justify-between text-sm font-medium uppercase relative z-20">
        <div class="flex justify-between items-center">
            <span class="opacity-50" id="clock-day">—</span>
            <span id="clock-date">—</span>
        </div>
        <div class="text-center">
            <div class="text-5xl font-display" id="current-time">--:--</div>
        </div>
        <div class="flex justify-between items-center">
            <span class="opacity-50">{{ $temperature }}</span>
            <span>{{ $location }}</span>
            <span class="badge badge-secondary">{{ $timezone }}</span>
        </div>
        @if($bgImage)
            <div class="absolute inset-0 -z-10">
                <img src="{{ $bgImage }}" alt="Blueprint" class="w-full h-full object-contain">
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    (function () {
        function updateClock() {
            const now = new Date();
            const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
            const months = ['January','February','March','April','May','June',
                            'July','August','September','October','November','December'];

            document.getElementById('clock-day').textContent = days[now.getDay()];
            document.getElementById('clock-date').textContent =
                months[now.getMonth()] + ' ' + now.getDate() + getSuffix(now.getDate()) + ', ' + now.getFullYear();
            document.getElementById('current-time').textContent =
                now.toLocaleTimeString('en-US', { hour12: true, hour: 'numeric', minute: '2-digit' });
        }

        function getSuffix(d) {
            if (d > 3 && d < 21) return 'th';
            switch (d % 10) { case 1: return 'st'; case 2: return 'nd'; case 3: return 'rd'; default: return 'th'; }
        }

        setInterval(updateClock, 1000);
        updateClock();
    })();
</script>
@endpush
