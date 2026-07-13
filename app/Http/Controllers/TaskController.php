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
            $question = $quiz->generateQuestion(auth()->id(), $order->ID, $order->task_category);
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

        // Split the order's daily reward evenly across its tasks-per-day, crediting
        // whatever's left of the daily amount on the final task of the batch so the
        // day's total always sums to exactly `daily` regardless of rounding.
        $tasksClaimedBefore = $order->tasksClaimedToday();
        $tasksPerDay        = max(1, $order->tasks_per_day);
        $perTaskAmount      = floor(($order->daily / $tasksPerDay) * 100) / 100;
        $isLastTaskOfBatch  = ($tasksClaimedBefore + 1) >= $tasksPerDay;
        $taskAmount         = $isLastTaskOfBatch
            ? $order->daily - ($perTaskAmount * ($tasksPerDay - 1))
            : $perTaskAmount;

        $newEarnings = $order->earnings + $taskAmount;
        $capped      = min($newEarnings, $order->totals);
        $credited    = $capped - $order->earnings;

        $order->earnings             = $capped;
        $order->status               = $capped >= $order->totals ? 'Inactive' : 'Active';
        $order->tasks_claimed_today  = $tasksClaimedBefore + 1;
        $order->last_claimed_at      = now();
        $order->save();

        $earnings->balance += $credited;
        $earnings->totals  += $credited;
        $earnings->save();

        $rate = (float) Setting::get('usd_kes_rate', 130);

        return back()->with('success', 'Reward claimed! +$' . number_format($credited / $rate, 2));
    }
}
