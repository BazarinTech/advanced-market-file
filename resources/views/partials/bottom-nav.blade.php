<nav class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-150 bg-[#0a1929] border-t border-[#1a3a4a] flex z-50">
    <a href="{{ route('home') }}"
       class="flex flex-col items-center justify-center flex-1 py-3 no-underline border-r border-[#1a3a4a] {{ request()->routeIs('home') ? 'text-[#00c9a7]' : 'text-[#64899a] hover:text-[#00c9a7]' }}">
        <x-icon name="chart-line" class="text-base" />
        <span class="text-[10px] mt-1 tracking-widest uppercase">Home</span>
    </a>
    <a href="{{ route('packages') }}"
       class="flex flex-col items-center justify-center flex-1 py-3 no-underline border-r border-[#1a3a4a] {{ request()->routeIs('packages') ? 'text-[#00c9a7]' : 'text-[#64899a] hover:text-[#00c9a7]' }}">
        <x-icon name="layer-group" class="text-base" />
        <span class="text-[10px] mt-1 tracking-widest uppercase">Plans</span>
    </a>
    <a href="{{ route('task') }}"
       class="flex flex-col items-center justify-center flex-1 py-3 no-underline border-r border-[#1a3a4a] {{ request()->routeIs('task') ? 'text-[#00c9a7]' : 'text-[#64899a] hover:text-[#00c9a7]' }}">
        <x-icon name="briefcase" class="text-base" />
        <span class="text-[10px] mt-1 tracking-widest uppercase">Orders</span>
    </a>
    <a href="{{ route('account') }}"
       class="flex flex-col items-center justify-center flex-1 py-3 no-underline {{ request()->routeIs('account') ? 'text-[#00c9a7]' : 'text-[#64899a] hover:text-[#00c9a7]' }}">
        <x-icon name="user" class="text-base" />
        <span class="text-[10px] mt-1 tracking-widest uppercase">Account</span>
    </a>
</nav>
