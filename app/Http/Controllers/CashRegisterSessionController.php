<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\CashRegisterSession;
use Illuminate\Http\Request;

class CashRegisterSessionController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:cash-register-session.view-any')->only('index');
        $this->middleware('can:cash-register-session.open')->only('open');
        $this->middleware('can:cash-register-session.close')->only('close');
    }

    public function index()
    {
        $sessions = CashRegisterSession::with(['cashRegister', 'user'])->latest()->paginate(10);

        return view('cash-register-sessions.index', compact('sessions'));
    }

    public function open(Request $request, CashRegister $cashRegister)
    {
        $existingOpen = CashRegisterSession::where('cash_register_id', $cashRegister->id)
            ->where('status', 'open')
            ->first();

        if ($existingOpen) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'This cash register already has an open session.'], 400);
            }
            session()->flash('error', 'This cash register already has an open session.');
            return redirect()->back();
        }

        $validated = $request->validate([
            'opening_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $session = CashRegisterSession::create([
            'cash_register_id' => $cashRegister->id,
            'user_id' => auth()->id(),
            'opening_balance' => $validated['opening_balance'],
            'opening_date' => now(),
            'status' => 'open',
            'notes' => $validated['notes'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Session opened successfully.', 'session' => $session], 201);
        }

        session()->flash('success', 'Session opened successfully.');
        return redirect()->route('cash-register-sessions.index');
    }

    public function close(Request $request, CashRegisterSession $cashRegisterSession)
    {
        if ($cashRegisterSession->status === 'closed') {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Session is already closed.'], 400);
            }
            session()->flash('error', 'Session is already closed.');
            return redirect()->back();
        }

        $validated = $request->validate([
            'closing_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $cashRegisterSession->update([
            'closing_balance' => $validated['closing_balance'],
            'closing_date' => now(),
            'status' => 'closed',
            'notes' => $validated['notes'] ?? $cashRegisterSession->notes,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Session closed successfully.', 'session' => $cashRegisterSession]);
        }

        session()->flash('success', 'Session closed successfully.');
        return redirect()->route('cash-register-sessions.index');
    }
}
