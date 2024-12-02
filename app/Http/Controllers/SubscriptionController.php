<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\SubscriptionCategory;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Show available subscription categories.
     */
    public function index()
    {
        $categories = SubscriptionCategory::all();

        return view('subscriptions.index', compact('categories'));
    }

    /**
     * Show the subscription form for a selected category.
     */
    public function create($categoryId)
    {
        $category = SubscriptionCategory::findOrFail($categoryId);

        return view('subscriptions.create', compact('category'));
    }

    /**
     * Store a new subscription.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subscription_category_id' => 'required|exists:subscription_categories,id',
            'paid' => 'required|boolean',
            'payment_transaction' => 'nullable|string',
        ]);

        $category = SubscriptionCategory::findOrFail($request->subscription_category_id);

        $subscription = Subscription::create([
            'user_id' => Auth::id(),
            'subscription_category_id' => $category->id,
            'paid' => $request->paid,
            'payment_transaction' => $request->paid ? $request->payment_transaction : null,
            'start_time' => now(),
            'end_time' => now()->addSeconds($category->expiry),
        ]);

        return redirect()->route('subscriptions.show', $subscription->id)
            ->with('success', 'Subscription created successfully!');
    }

    /**
     * View details of a specific subscription.
     */
    public function show($id)
    {
        $subscription = Subscription::with('subscriptionCategory')->findOrFail($id);

        return view('subscriptions.show', compact('subscription'));
    }

    /**
     * Update payment status for a subscription.
     */
    public function updatePayment(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);

        $request->validate([
            'paid' => 'required|boolean',
            'payment_transaction' => 'nullable|string',
        ]);

        $subscription->update([
            'paid' => $request->paid,
            'payment_transaction' => $request->paid ? $request->payment_transaction : null,
        ]);

        return redirect()->route('subscriptions.show', $id)
            ->with('success', 'Payment status updated successfully!');
    }

    /**
     * Cancel a subscription.
     */
    public function cancel($id)
    {
        $subscription = Subscription::findOrFail($id);

        $subscription->delete();

        return redirect()->route('subscriptions.index')
            ->with('success', 'Subscription canceled successfully!');
    }
}
