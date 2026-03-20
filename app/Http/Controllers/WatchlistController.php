<?php

namespace App\Http\Controllers;

use App\Http\Requests\WatchlistItemRequest;
use App\Models\WatchlistItem;
use Illuminate\Http\Request;

class WatchlistController extends Controller
{
    /**
     * Listado con filtros y estadísticas.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['type', 'status', 'search', 'platform']);

        $items = WatchlistItem::query()
            ->filter($filters)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Stats rápidas
        $stats = [
            'total'     => WatchlistItem::count(),
            'movies'    => WatchlistItem::movies()->count(),
            'series'    => WatchlistItem::series()->count(),
            'pending'   => WatchlistItem::status('pending')->count(),
            'watching'  => WatchlistItem::status('watching')->count(),
            'completed' => WatchlistItem::status('completed')->count(),
        ];

        return view('pages.watchlist.index', compact('items', 'filters', 'stats'));
    }

    /**
     * Formulario de creación.
     */
    public function create()
    {
        return view('pages.watchlist.create');
    }

    /**
     * Guardar nuevo item.
     */
    public function store(WatchlistItemRequest $request)
    {
        WatchlistItem::create($request->validated());

        return redirect()
            ->route('watchlist.index')
            ->with('success', '¡Agregado a tu watchlist!');
    }

    /**
     * Detalle de un item.
     */
    public function show(WatchlistItem $watchlist)
    {
        return view('pages.watchlist.show', ['item' => $watchlist]);
    }

    /**
     * Formulario de edición.
     */
    public function edit(WatchlistItem $watchlist)
    {
        return view('pages.watchlist.edit', ['item' => $watchlist]);
    }

    /**
     * Actualizar item.
     */
    public function update(WatchlistItemRequest $request, WatchlistItem $watchlist)
    {
        $watchlist->update($request->validated());

        return redirect()
            ->route('watchlist.show', $watchlist)
            ->with('success', 'Actualizado correctamente.');
    }

    /**
     * Eliminar item.
     */
    public function destroy(WatchlistItem $watchlist)
    {
        $watchlist->delete();

        return redirect()
            ->route('watchlist.index')
            ->with('success', 'Eliminado de tu watchlist.');
    }

    /**
     * Cambio rápido de status (AJAX-friendly).
     */
    public function toggleStatus(Request $request, WatchlistItem $watchlist)
    {
        $request->validate([
            'status' => ['required', 'in:' . implode(',', array_keys(WatchlistItem::STATUSES))],
        ]);

        $watchlist->update(['status' => $request->status]);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'status' => $watchlist->status_label]);
        }

        return back()->with('success', "Estado cambiado a: {$watchlist->status_label}");
    }
}
