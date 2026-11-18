<?php

namespace App\Modules\TicketModule\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\TicketModule\Http\Requests\StoreTicketRequest;
use App\Modules\TicketModule\Models\Ticket;
use Illuminate\Http\RedirectResponse;

class TicketController extends Controller
{
    public function store(StoreTicketRequest $request): RedirectResponse
    {
        Ticket::create($request->validated());

        return back()->with('success', 'Jenis tiket baru berhasil ditambahkan ke event!');
    }

    public function destroy(int $id): RedirectResponse
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();

        return back()->with('success', 'Jenis tiket berhasil dihapus.');
    }
}
