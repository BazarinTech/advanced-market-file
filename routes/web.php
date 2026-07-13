<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WithdrawController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\CouponController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\WithdrawalAccountController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\WalletController;
use App\Http\Controllers\PayoutCallbackController;

// Setup wizard (blocked once installed)
Route::middleware('not.installed')->group(function () {
    Route::get('/setup',         [\App\Http\Controllers\SetupController::class, 'index'])->name('setup.index');
    Route::post('/setup/test-db',[\App\Http\Controllers\SetupController::class, 'testDb'])->name('setup.test-db');
    Route::post('/setup/install',[\App\Http\Controllers\SetupController::class, 'install'])->name('setup.install');
});

// Root redirect
Route::get('/', fn () => redirect()->route('register'));

// Auth routes (guests only)
Route::middleware('guest')->group(function () {
    Route::get('/login',           [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',          [AuthController::class, 'login']);
    Route::get('/register',        [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',       [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgot'])->name('forgot');
    Route::post('/forgot-password',[AuthController::class, 'submitForgot'])->name('forgot.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated user routes
Route::middleware('auth')->group(function () {
    Route::get('/home',        [DashboardController::class, 'home'])->name('home');
    Route::get('/packages',    [DashboardController::class, 'packages'])->name('packages');
    Route::post('/home',       [DashboardController::class, 'buyPackage'])->name('home.buy');
    Route::get('/account',     [DashboardController::class, 'account'])->name('account');
    Route::get('/user',        [DashboardController::class, 'userSettings'])->name('user');
    Route::post('/user',       [DashboardController::class, 'updateSettings'])->name('user.update');

    Route::get('/task',        [TaskController::class, 'index'])->name('task');
    Route::post('/task',       [TaskController::class, 'claim'])->name('task.claim');

    Route::get('/team',        [TeamController::class, 'index'])->name('team');
    Route::get('/packages/table', [DashboardController::class, 'packagesTable'])->name('packages.table');
    Route::get('/transaction', [TransactionController::class, 'index'])->name('transaction');

    // reward.php immediately redirected to home — keep same behaviour
    Route::get('/reward', fn() => redirect()->route('home'))->name('reward');

    Route::get('/deposit',  [DepositController::class, 'show'])->name('deposit');
    Route::post('/deposit', [DepositController::class, 'store'])->name('deposit.store');

    Route::get('/withdraw',        [WithdrawController::class, 'show'])->name('withdraw');
    Route::post('/withdraw',       [WithdrawController::class, 'store'])->name('withdraw.store');
    Route::post('/withdraw/setup', [WithdrawController::class, 'setupAccount'])->name('withdraw.setup');

    Route::get('/coupon',  [CouponController::class, 'show'])->name('coupon');
    Route::post('/coupon', [CouponController::class, 'redeem'])->name('coupon.redeem');
});

// Payment webhooks — CSRF excluded via bootstrap/app.php validateCsrfTokens(except:[...])
Route::post('/callback',     [PaymentCallbackController::class, 'handle'])->name('callback');
Route::post('/callback/b2c', [PayoutCallbackController::class,  'handle'])->name('callback.b2c');

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard',                   [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/users',                       [Admin\UserController::class, 'index'])->name('users');
    Route::post('/users/{id}/status',          [Admin\UserController::class, 'updateStatus'])->name('users.status');
    Route::post('/users/{id}/make-admin',      [Admin\UserController::class, 'makeAdmin'])->name('users.make-admin');
    Route::post('/users/{id}/reset-password',  [Admin\UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::get('/recovery-requests',           [Admin\UserController::class, 'recoveryRequests'])->name('recovery-requests');
    Route::post('/recovery-requests/{id}/resolve', [Admin\UserController::class, 'resolveRecovery'])->name('recovery-requests.resolve');
    Route::get('/deposits',                    [Admin\TransactionController::class, 'deposits'])->name('deposits');
    Route::post('/deposits',                   [Admin\TransactionController::class, 'manualDeposit'])->name('deposits.store');
    Route::get('/withdrawals',                 [Admin\TransactionController::class, 'withdrawals'])->name('withdrawals');
    Route::post('/withdrawals/{id}/approve',   [Admin\TransactionController::class, 'approveWithdrawal'])->name('withdrawals.approve');
    Route::post('/withdrawals/{id}/reject',    [Admin\TransactionController::class, 'rejectWithdrawal'])->name('withdrawals.reject');

    Route::get('/withdrawal-accounts',              [WithdrawalAccountController::class, 'index'])->name('withdrawal-accounts');
    Route::post('/withdrawal-accounts/{account}',   [WithdrawalAccountController::class, 'update'])->name('withdrawal-accounts.update');

    Route::get('/settings',         [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings',        [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/banner',      [SettingsController::class, 'updateBanner'])->name('settings.banner');
    Route::post('/settings/claim-image', [SettingsController::class, 'updateClaimImage'])->name('settings.claim-image');
    Route::post('/settings/links',       [SettingsController::class, 'updateLinks'])->name('settings.links');
    Route::post('/settings/logo',        [SettingsController::class, 'updateLogo'])->name('settings.logo');

    Route::get('/wallets',         [WalletController::class, 'index'])->name('wallets');
    Route::get('/wallets/{wallet}', [WalletController::class, 'edit'])->name('wallets.edit');
    Route::post('/wallets/{wallet}', [WalletController::class, 'update'])->name('wallets.update');

    Route::get('/packages',               [PackageController::class, 'index'])->name('packages');
    Route::post('/packages',              [PackageController::class, 'store'])->name('packages.store');
    Route::post('/packages/{package}',    [PackageController::class, 'update'])->name('packages.update');
    Route::post('/packages/{package}/delete', [PackageController::class, 'destroy'])->name('packages.destroy');

    Route::get('/coupons',                    [\App\Http\Controllers\Admin\CouponController::class, 'index'])->name('coupons.index');
    Route::post('/coupons',                   [\App\Http\Controllers\Admin\CouponController::class, 'store'])->name('coupons.store');
    Route::delete('/coupons/{coupon}',        [\App\Http\Controllers\Admin\CouponController::class, 'destroy'])->name('coupons.destroy');
});
