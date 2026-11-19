<?php

namespace App\Modules\RegistrationModule\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\RegistrationModule\Models\Registration;
use App\Modules\RegistrationModule\Services\RegistrationService;
use App\Modules\TicketModule\Exceptions\QuotaFullException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function __construct(
        protected RegistrationService $registrationService
    ) {}

    public function index(): View
    {
        $registrations = Registration::with(['ticket.event'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('registrations.index', compact('registrations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ticket_id' => ['required', 'integer', 'exists:tickets,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $qty = (int) $validated['quantity'];

            $this->registrationService->bookTicket(
                Auth::id(),
                (int) $validated['ticket_id'],
                $qty
            );

            return redirect()->route('registrations.index')
                ->with('success', "{$qty} E-Ticket berhasil diterbitkan! Masing-masing tiket memiliki kode & QR Code unik.");
        } catch (QuotaFullException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->registrationService->cancelBooking($id, Auth::id());

        return redirect()->route('registrations.index')
            ->with('success', 'Tiket berhasil dibatalkan dan 1 kuota telah dikembalikan.');
    }

    public function checkIn(Request $request, int $eventId): RedirectResponse
    {
        $validated = $request->validate([
            'ticket_code' => ['required', 'string', 'max:32'],
        ]);

        $result = $this->registrationService->checkInTicket($eventId, $validated['ticket_code']);

        if ($result['status'] === 'valid') {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }
}
