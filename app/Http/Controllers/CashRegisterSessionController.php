<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\CashRegisterSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $validated = $request->validate([
            'opening_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        try {
            $session = DB::transaction(function () use ($cashRegister, $validated) {
                $existingOpen = CashRegisterSession::where('cash_register_id', $cashRegister->id)
                    ->where('status', 'open')
                    ->lockForUpdate()
                    ->first();

                if ($existingOpen) {
                    throw new \RuntimeException('Esta caja registradora ya tiene una sesión abierta.');
                }

                return CashRegisterSession::create([
                    'cash_register_id' => $cashRegister->id,
                    'user_id' => auth()->id(),
                    'opening_balance' => $validated['opening_balance'],
                    'opening_date' => now(),
                    'status' => 'open',
                    'notes' => $validated['notes'] ?? null,
                ]);
            });

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Session opened successfully.', 'session' => $session], 201);
            }

            toast('Sesión abierta correctamente.', 'success');
            return redirect()->route('cash-register-sessions.index');
        } catch (\RuntimeException $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
            toast($e->getMessage(), 'error', true);
            return redirect()->back();
        }
    }

    public function close(Request $request, CashRegisterSession $cashRegisterSession)
    {
        if ($cashRegisterSession->status === 'closed') {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Session is already closed.'], 400);
            }
            toast('Session is already closed.', 'error', true);
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

        toast('Session closed successfully.', 'success');
        return redirect()->route('cash-register-sessions.index');
    }
}
