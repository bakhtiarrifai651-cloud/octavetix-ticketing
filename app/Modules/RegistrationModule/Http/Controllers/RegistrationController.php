<?php

namespace App\Modules\RegistrationModule\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\RegistrationModule\Http\Requests\StoreRegistrationRequest;
use App\Modules\RegistrationModule\Models\Registration;
use App\Modules\RegistrationModule\Services\RegistrationService;
use App\Modules\TicketModule\Exceptions\QuotaFullException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function index(): View
    {
        $registrations = Registration::with(['ticket.event'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('registrations.index', compact('registrations'));
    }

    public function store(StoreRegistrationRequest $request, RegistrationService $registrationService): RedirectResponse
    {
        $data = $request->validated();

        try {
            $registrationService->bookTicket(
                Auth::id(),
                (int) $data['ticket_id'],
                (int) $data['quantity']
            );

            return redirect()->route('registrations.index')
                ->with('success', 'Pemesanan tiket berhasil! Kuota tiket otomatis diperbarui.');
        } catch (QuotaFullException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}