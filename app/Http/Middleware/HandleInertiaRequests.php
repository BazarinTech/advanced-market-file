<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    ...$user->only(['ID', 'email', 'phone', 'status', 'refer', 'country', 'role']),
                    'earnings' => $user->earnings,
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'success_withdrawal' => fn () => $request->session()->get('success_withdrawal'),
                'success_banner' => fn () => $request->session()->get('success_banner'),
                'success_links' => fn () => $request->session()->get('success_links'),
                'success_claim_image' => fn () => $request->session()->get('success_claim_image'),
                'success_logo' => fn () => $request->session()->get('success_logo'),
                'success_crypto' => fn () => $request->session()->get('success_crypto'),
            ],
            'platformLogo' => fn () => Setting::get('logo'),
            'usdRate' => fn () => (float) Setting::get('usd_kes_rate', 130),
        ];
    }
}
