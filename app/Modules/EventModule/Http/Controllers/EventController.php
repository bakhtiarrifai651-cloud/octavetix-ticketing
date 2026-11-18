<?php

namespace App\Modules\EventModule\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\EventModule\Models\Category;
use App\Modules\EventModule\Models\Event;
use App\Modules\RegistrationModule\Contracts\RegistrationContract;
use App\Modules\TicketModule\Contracts\TicketContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EventController extends Controller
{
    public function __construct(
        protected TicketContract $ticketContract,
        protected RegistrationContract $registrationContract
    ) {}

    public function index(Request $request): View
    {
        $query = Event::with('categories')->orderBy('event_date', 'asc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $categoryFilter = $request->input('category');
            $query->whereHas('categories', function ($q) use ($categoryFilter) {
                $q->where('slug', $categoryFilter)
                    ->orWhere('categories.id', $categoryFilter);
            });
        }

        $events = $query->get();
        $categories = Category::orderBy('name')->get();

        return view('events.index', compact('events', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('events.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'event_date' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
        ]);

        $event = Event::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'event_date' => $validated['event_date'],
            'location' => $validated['location'],
        ]);

        $event->categories()->sync($validated['categories'] ?? []);

        return redirect()->route('events.index')
            ->with('success', 'Gigs baru berhasil diterbitkan ke OctaveTix!');
    }

    public function show(int $id): View
    {
        $event = Event::with('categories')->findOrFail($id);
        $tickets = $this->ticketContract->getTicketsByEvent($event->id);

        $participantReport = null;
        if (Auth::check() && Auth::user()->isAdmin()) {
            $participantReport = $this->registrationContract->getEventParticipants($event->id);
        }

        return view('events.show', compact('event', 'tickets', 'participantReport'));
    }

    public function edit(int $id): View
    {
        $event = Event::with('categories')->findOrFail($id);
        $categories = Category::orderBy('name')->get();

        return view('events.edit', compact('event', 'categories'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $event = Event::findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'event_date' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
        ]);

        $event->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'event_date' => $validated['event_date'],
            'location' => $validated['location'],
        ]);

        $event->categories()->sync($validated['categories'] ?? []);

        return redirect()->route('events.show', $event->id)
            ->with('success', 'Detail gigs berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $event = Event::findOrFail($id);
        $this->ticketContract->deleteTicketsByEvent($event->id);
        $event->categories()->detach();
        $event->delete();

        return redirect()->route('events.index')
            ->with('success', 'Event beserta seluruh jenis tiketnya berhasil dihapus.');
    }
}
