@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('content')

<h1 class="text-2xl font-bold text-gray-800 mb-4">Admin Dashboard</h1>

<div class="flex flex-wrap gap-2 mb-6">
    <a href="{{ route('admin.deposits') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Deposits</a>
    <a href="{{ route('admin.withdrawals') }}" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Withdrawals</a>
    <a href="{{ route('admin.users') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Users</a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach([
        ['Payment Balance',    'Kes ' . number_format(0, 2),           'border-purple-400', 'money'],
        ['Total Withdrawals',  'Kes ' . number_format($totalWith, 2),  'border-blue-400',   'money-bill-trend-up'],
        ['Total Account Bals', 'Kes ' . number_format($totalBals, 2),  'border-yellow-400', 'money-bill-trend-up'],
        ['Total Deposits',     'Kes ' . number_format($totalDeps, 2),  'border-amber-400',  'money-bill-1-wave'],
        ['Active Users',       $active,                                 'border-purple-400', 'users'],
        ['Total Users',        $totalUsers,                             'border-green-400',  'user'],
        ['Joined Today',       $joinedToday,                            'border-green-400',  'user'],
        ['Users Deposited',    $usersDeposited,                         'border-amber-400',  'circle-check'],
    ] as [$label, $value, $border, $icon])
    <div class="bg-white rounded-xl shadow p-4 flex items-center justify-between border-l-4 {{ $border }}">
        <div>
            <p class="text-green-600 text-sm">{{ $label }}</p>
            <p class="font-bold text-gray-700 text-lg">{{ $value }}</p>
        </div>
        <span class="text-gray-300 text-3xl"><x-icon name="{{ $icon }}" /></span>
    </div>
    @endforeach
</div>
@endsection
