{{-- resources/views/components/monky/sidebar.blade.php --}}
@props(['currentRoute' => 'overview'])

{{-- ═══════════════════════════════════════════════════════════════════════
     MOBILE TOP BAR — visible solo en < lg
     ═══════════════════════════════════════════════════════════════════════ --}}
<div class="lg:hidden fixed top-0 left-0 right-0 z-40 bg-background border-b-2 border-border">
    <div class="flex items-center justify-between px-4 py-3">
        {{-- Brand compact --}}
        <div class="flex items-center gap-2.5">
            <div class="flex size-9 items-center justify-center rounded bg-primary text-primary-foreground">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/>
                </svg>
            </div>
            <span class="text-lg font-display">M.O.N.K.Y.</span>
        </div>

        {{-- Hamburger button --}}
        <button
            id="sidebar-toggle"
            class="flex items-center justify-center size-10 rounded border border-border bg-accent transition-colors hover:bg-secondary"
            aria-label="Abrir menú"
        >
            <svg id="icon-hamburger" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
            <svg id="icon-close" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="hidden">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════
     BACKDROP — overlay oscuro en mobile
     ═══════════════════════════════════════════════════════════════════════ --}}
<div
    id="sidebar-backdrop"
    class="lg:hidden fixed inset-0 z-40 bg-black/60 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300"
></div>

{{-- ═══════════════════════════════════════════════════════════════════════
     SIDEBAR — desktop: sticky | mobile: drawer slide-in
     ═══════════════════════════════════════════════════════════════════════ --}}
<div
    id="sidebar-drawer"
    class="
        sidebar p-4 sticky top-4
        max-lg:fixed max-lg:inset-y-0 max-lg:left-0 max-lg:z-50
        max-lg:w-72 max-lg:top-0 max-lg:shadow-2xl
        max-lg:-translate-x-full max-lg:transition-transform max-lg:duration-300 max-lg:ease-out
        max-lg:overflow-y-auto
        max-lg:bg-background max-lg:border-r-2 max-lg:border-border
    "
>
    {{-- Brand --}}
    <div class="flex gap-3 mb-6">
        <div class="flex size-12 items-center justify-center rounded bg-primary text-primary-foreground">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/>
            </svg>
        </div>
        <div class="flex-1">
            <div class="text-2xl font-display">M.O.N.K.Y.</div>
            <div class="text-xs uppercase opacity-50">The OS for Rebels</div>
        </div>

        {{-- Close button (mobile only) --}}
        <button
            id="sidebar-close"
            class="lg:hidden flex items-center justify-center size-8 rounded border border-border bg-accent hover:bg-secondary transition-colors self-start"
            aria-label="Cerrar menú"
        >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>

    {{-- Navigation --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-3 text-sm font-medium">
            <span class="bullet"></span>
            Tools
        </div>
        <nav class="space-y-1">
            <x-monky.sidebar-item
                href="{{ route('overview') }}"
                :active="request()->routeIs('overview')"
                label="Escritorio"
            >
                <x-slot:icon>
                    {{-- Grid / Dashboard --}}
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7" rx="1"/>
                        <rect x="14" y="3" width="7" height="7" rx="1"/>
                        <rect x="3" y="14" width="7" height="7" rx="1"/>
                        <rect x="14" y="14" width="7" height="7" rx="1"/>
                    </svg>
                </x-slot:icon>
            </x-monky.sidebar-item>

            <x-monky.sidebar-item
                href="{{ route('statements.index') }}"
                :active="request()->routeIs('statements.*')"
                label="Declaraciones"
            >
                <x-slot:icon>
                    {{-- File text / Statement --}}
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                </x-slot:icon>
            </x-monky.sidebar-item>

            <x-monky.sidebar-item
                href="{{ route('watchlist.index') }}"
                :active="request()->routeIs('watchlist.*')"
                label="Películas y Series"
            >
                <x-slot:icon>
                    {{-- Film / Clapperboard --}}
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"/>
                        <line x1="7" y1="2" x2="7" y2="22"/>
                        <line x1="17" y1="2" x2="17" y2="22"/>
                        <line x1="2" y1="12" x2="22" y2="12"/>
                        <line x1="2" y1="7" x2="7" y2="7"/>
                        <line x1="2" y1="17" x2="7" y2="17"/>
                        <line x1="17" y1="7" x2="22" y2="7"/>
                        <line x1="17" y1="17" x2="22" y2="17"/>
                    </svg>
                </x-slot:icon>
            </x-monky.sidebar-item>

            <x-monky.sidebar-item
                href="{{ route('media.index') }}"
                :active="request()->routeIs('media.*')"
                label="Archivos"
            >
                <x-slot:icon>
                    {{-- Folder --}}
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                    </svg>
                </x-slot:icon>
            </x-monky.sidebar-item>

            <x-monky.sidebar-item
                href="#"
                :locked="true"
                label="Admin Settings"
            >
                <x-slot:icon>
                    {{-- Settings gear --}}
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                    </svg>
                </x-slot:icon>
            </x-monky.sidebar-item>
        </nav>
    </div>

    {{-- User --}}
    <div>
        <div class="flex items-center gap-2 mb-3 text-sm font-medium">
            <span class="bullet"></span>
            User
        </div>
        <div class="flex gap-2">
            <div class="size-14 bg-primary rounded-lg overflow-hidden flex-shrink-0">
                <img
                    src="{{ auth()->user()->avatar_url ?? asset('avatars/user_krimson.png') }}"
                    alt="{{ auth()->user()->name ?? 'KRIMSON' }}"
                    class="w-full h-full object-cover"
                >
            </div>
            <div class="flex-1 bg-accent rounded p-2 min-w-0">
                <div class="text-xl font-display truncate">{{ strtoupper(auth()->user()->name ?? 'KRIMSON') }}</div>
                <div class="text-xs uppercase opacity-50 truncate">{{ auth()->user()->email ?? 'krimson@joyco.studio' }}</div>
            </div>
        </div>

        {{-- Logout — visible solo en mobile " --}}
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded text-sm text-muted-foreground hover:bg-accent transition-colors cursor-pointer">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Cerrar sesión
            </button>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════
     SPACER — empuja el contenido en mobile para no quedar tapado
     ═══════════════════════════════════════════════════════════════════════ --}}
<div class="lg:hidden h-[60px]"></div>

{{-- ═══════════════════════════════════════════════════════════════════════
     SCRIPT — toggle sidebar (vanilla JS, sin dependencias)
     ═══════════════════════════════════════════════════════════════════════ --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle    = document.getElementById('sidebar-toggle');
        const closeBtn  = document.getElementById('sidebar-close');
        const drawer    = document.getElementById('sidebar-drawer');
        const backdrop  = document.getElementById('sidebar-backdrop');
        const iconOpen  = document.getElementById('icon-hamburger');
        const iconClose = document.getElementById('icon-close');

        let isOpen = false;

        function openSidebar() {
            isOpen = true;
            drawer.classList.remove('max-lg:-translate-x-full');
            drawer.classList.add('max-lg:translate-x-0');
            backdrop.classList.remove('opacity-0', 'pointer-events-none');
            backdrop.classList.add('opacity-100', 'pointer-events-auto');
            iconOpen.classList.add('hidden');
            iconClose.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            isOpen = false;
            drawer.classList.add('max-lg:-translate-x-full');
            drawer.classList.remove('max-lg:translate-x-0');
            backdrop.classList.add('opacity-0', 'pointer-events-none');
            backdrop.classList.remove('opacity-100', 'pointer-events-auto');
            iconOpen.classList.remove('hidden');
            iconClose.classList.add('hidden');
            document.body.style.overflow = '';
        }

        if (toggle)   toggle.addEventListener('click', () => isOpen ? closeSidebar() : openSidebar());
        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if (backdrop) backdrop.addEventListener('click', closeSidebar);

        // Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && isOpen) closeSidebar();
        });

        // Soporte Turbo / Livewire (cierra al navegar)
        document.addEventListener('turbo:before-visit', closeSidebar);
        document.addEventListener('livewire:navigating', closeSidebar);
    });
</script>