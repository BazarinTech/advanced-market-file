<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Package;
use App\Models\Setting;
use App\Services\TaskQuizService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index()
    {
        $user   = auth()->user();
        $orders = Order::where('email', $user->email)->orderByDesc('ID')->get();

        // Load package images keyed by name
        $packageImages = Package::pluck('image', 'name');

        $claimImgFile = Setting::get('claim_image');
        $claimImgSrc  = $claimImgFile
            ? asset('images/' . $claimImgFile)
            : asset('images/orderL.jpeg');

        return Inertia::render('Dashboard/Task', [
            'orders'        => $orders,
            'packageImages' => $packageImages,
            'claimImgSrc'   => $claimImgSrc,
        ]);
    }

    protected function authorizeOrder(Order $order): void
    {
        abort_unless($order->email === auth()->user()->email, 403);
    }

    /**
     * Generate (and cache server-side) a quiz question for this order's
     * claim attempt. Returns only the question text — never the answer.
     */
    public function question(Order $order, TaskQuizService $quiz)
    {
        $this->authorizeOrder($order);

        if ($order->status !== 'Active' || ! $order->canClaim()) {
            return response()->json(['message' => 'This task is not ready to claim yet.'], 422);
        }

        try {
            $question = $quiz->generateQuestion(auth()->id(), $order->ID);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

        return response()->json(['question' => $question]);
    }

    /**
     * Verify the submitted answer via OpenAI and, only if correct, credit
     * this single order's daily reward to the user's balance.
     */
    public function claim(Request $request, Order $order, TaskQuizService $quiz)
    {
        $this->authorizeOrder($order);

        $request->validate([
            'answer' => 'required|string|max:255',
        ]);

        if ($order->status !== 'Active' || ! $order->canClaim()) {
            return back()->with('error', 'This task is not ready to claim yet.');
        }

        try {
            $correct = $quiz->checkAnswer(auth()->id(), $order->ID, $request->answer);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        if (! $correct) {
            return back()->with('error', "That response wasn't quite right. Please try again.");
        }

        $earnings = auth()->user()->earnings;

        $newEarnings = $order->earnings + $order->daily;
        $capped      = min($newEarnings, $order->totals);
        $credited    = $capped - $order->earnings;

        $order->earnings        = $capped;
        $order->status          = $capped >= $order->totals ? 'Inactive' : 'Active';
        $order->last_claimed_at = now();
        $order->save();

        $earnings->balance += $credited;
        $earnings->totals  += $credited;
        $earnings->save();

        return back()->with('success', 'Reward claimed! +Kes ' . number_format($credited, 2));
    }
}
