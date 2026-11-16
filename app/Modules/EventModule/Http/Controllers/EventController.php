<?php

namespace App\Modules\EventModule\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\EventModule\Http\Requests\EventRequest;
use App\Modules\EventModule\Models\Category;
use App\Modules\EventModule\Models\Event;
use App\Modules\TicketModule\Contracts\TicketContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::orderBy('name')->get();

        $events = Event::with('categories')
            ->when($request->category, function ($query, $slug) {
                $query->whereHas('categories', fn ($q) => $q->where('slug', $slug));
            })
            ->orderBy('event_date', 'asc')
            ->get();

        return view('events.index', compact('events', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('events.create', compact('categories'));
    }

    public function store(EventRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $event = Event::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'event_date' => $data['event_date'],
            'location' => $data['location'],
        ]);

        $event->categories()->sync($data['categories']);

        return redirect()->route('events.index')
            ->with('success', 'Event baru berhasil dibuat!');
    }

    public function show(int $id, TicketContract $ticketContract): View
    {
        $event = Event::with('categories')->findOrFail($id);

        // Mengambil daftar tiket dari TicketModule melalui Contract (Interface)
        $tickets = $ticketContract->getTicketsByEvent($event->id);

        return view('events.show', compact('event', 'tickets'));
    }

    public function edit(int $id): View
    {
        $event = Event::with('categories')->findOrFail($id);
        $categories = Category::orderBy('name')->get();

        return view('events.edit', compact('event', 'categories'));
    }

    public function update(EventRequest $request, int $id): RedirectResponse
    {
        $event = Event::findOrFail($id);
        $data = $request->validated();

        $event->update([
            'title' => $data['title'],
            'description' => $data['description'],
            'event_date' => $data['event_date'],
            'location' => $data['location'],
        ]);

        $event->categories()->sync($data['categories']);

        return redirect()->route('events.show', $event->id)
            ->with('success', 'Data event berhasil diperbarui!');
    }

    public function destroy(int $id): RedirectResponse
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return redirect()->route('events.index')
            ->with('success', 'Event berhasil dihapus.');
    }
}