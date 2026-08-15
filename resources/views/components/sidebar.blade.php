@props([
'devices'
])

<aside id="sidebar" class="fixed left-0 top-0 bottom-0 h-lvh w-[400px] bg-[#171717]  z-[1114] p-11 flex flex-col justify-between  -translate-x-full transition-transform duration-300">
    <div>
        <div class=" flex items-center justify-between">
            <div class="flex items-center gap-4 text-2xl">
                <img src="{{ asset('images/ecolume-logo.svg') }}" alt="" class="w-8">
                <h1>Ecolume</h1>
            </div>
            <button id="btn-close-sidebar" class="p-1.5 text-white hover:text-white rounded-md hover:bg-zinc-800 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="9" y1="3" x2="9" y2="21"></line>
                </svg>
            </button>
        </div>
        <hr class="my-8 -m-11 border-[#333333]">
        <p class="text-[#979797]">Menu</p>
        <a href="{{ route('beranda') }}" class="block mb-3">
            <div class="group flex items-center gap-[10px] px-3 py-2 hover:bg-[#2A2A2A] text-[#979797] hover:text-white rounded-xl">
                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg" class="fill-[#979797] group-hover:fill-white">
                    <path d="M9.16639 17.4167V12.8333H12.8331V17.4167C12.8331 17.9208 13.2456 18.3333 13.7497 18.3333H16.4997C17.0039 18.3333 17.4164 17.9208 17.4164 17.4167V11H18.9747C19.3964 11 19.5981 10.4775 19.2772 10.2025L11.6139 3.3C11.2656 2.98834 10.7339 2.98834 10.3856 3.3L2.72222 10.2025C2.41056 10.4775 2.60306 11 3.02472 11H4.58306V17.4167C4.58306 17.9208 4.99556 18.3333 5.49972 18.3333H8.24972C8.75389 18.3333 9.16639 17.9208 9.16639 17.4167Z" />
                </svg>
                <h1>Beranda</h1>
            </div>
        </a>

        <div class="mb-3" data-dropdown-group>
            <button
                type="button"
                class="dropdown-toggle w-full flex items-center justify-between px-3 py-2 rounded-xl bg-[#2A2A2A] text-white"
                data-dropdown-target="dashboard-dropdown-content"
                data-dropdown-icon="dashboard-dropdown-icon"
                aria-expanded="true">
                <div class="flex items-center gap-3">
                    <!-- Grid Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                        <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                        <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                        <rect x="14" y="14" width="7" height="7" rx="1"></rect>
                    </svg>
                    <span>Dashboard</span>
                </div>
                <!-- Arrow -->
                <svg id="dashboard-dropdown-icon"
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4 text-gray-400 transition-transform duration-200 rotate-180"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24">
                    <path d="M6 9l6 6 6-6" />
                </svg>
            </button>
            <div id="dashboard-dropdown-content"
                class="overflow-hidden transition-all duration-200 ease-in-out">
                <div class="ml-5 mt-2 border-l border-gray-700 pl-7 space-y-2">
                    @foreach($devices as $device_)
                    <a href="{{ route('dashboard', ['device_id' => $device_->id, 'device_name' => $device_->device_name]) }}" class="flex gap-2 py-2 hover:text-white hover:underline">
                        <span>{{ $device_->device_name }}</span>
                        <span class="w-[6px] h-[7px] rounded-full {{ $device_->online_status ? 'bg-[#00A451]' : 'bg-[#DC2626]'}}"></span>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mb-3" data-dropdown-group>
            <button
                type="button"
                class="dropdown-toggle w-full flex items-center justify-between px-3 py-2 rounded-xl hover:bg-[#2A2A2A] text-[#979797] hover:text-white"
                data-dropdown-target="cuaca-dropdown-content"
                data-dropdown-icon="cuaca-dropdown-icon"
                aria-expanded="false">
                <div class="flex items-center gap-[13px]">
                    <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg" class="fill-[#979797] group-hover:fill-white">
                        <path d="M15.4097 8.3125C15.9045 8.3125 16.3683 8.40527 16.8013 8.59082C17.2342 8.77637 17.6146 9.02686 17.9424 9.34229C18.2702 9.65771 18.5269 10.035 18.7124 10.4741C18.8979 10.9132 18.9938 11.3802 19 11.875C19 12.3698 18.9072 12.8306 18.7217 13.2573C18.5361 13.6841 18.2826 14.0614 17.9609 14.3892C17.6393 14.717 17.259 14.9736 16.8198 15.1592C16.3807 15.3447 15.9199 15.4375 15.4375 15.4375H4.75C4.0944 15.4375 3.479 15.3138 2.90381 15.0664C2.32861 14.819 1.82454 14.4819 1.3916 14.0552C0.958659 13.6284 0.61849 13.1243 0.371094 12.543C0.123698 11.9616 0 11.3431 0 10.6875C0 10.0319 0.123698 9.4165 0.371094 8.84131C0.61849 8.26611 0.955566 7.76204 1.38232 7.3291C1.80908 6.89616 2.31315 6.55599 2.89453 6.30859C3.47591 6.0612 4.0944 5.9375 4.75 5.9375C5.03451 5.9375 5.3221 5.96533 5.61279 6.021C5.86019 5.63753 6.14469 5.29427 6.46631 4.99121C6.78792 4.68815 7.14355 4.42839 7.5332 4.21191C7.92285 3.99544 8.33105 3.83464 8.75781 3.72949C9.18457 3.62435 9.62988 3.56868 10.0938 3.5625C10.7803 3.5625 11.4266 3.68311 12.0327 3.92432C12.6388 4.16553 13.18 4.49642 13.6562 4.91699C14.1325 5.33757 14.519 5.84163 14.8159 6.4292C15.1128 7.01676 15.3107 7.64453 15.4097 8.3125ZM15.4375 14.25C15.7653 14.25 16.0715 14.1882 16.356 14.0645C16.6405 13.9408 16.894 13.7707 17.1167 13.5542C17.3394 13.3377 17.5094 13.0872 17.627 12.8027C17.7445 12.5182 17.8063 12.209 17.8125 11.875C17.8125 11.5472 17.7507 11.241 17.627 10.9565C17.5033 10.672 17.3332 10.4185 17.1167 10.1958C16.9002 9.97314 16.6497 9.80306 16.3652 9.68555C16.0807 9.56803 15.7715 9.50619 15.4375 9.5H14.25V8.90625C14.25 8.33105 14.1418 7.79297 13.9253 7.29199C13.7088 6.79102 13.4119 6.3488 13.0347 5.96533C12.6574 5.58187 12.2183 5.28499 11.7173 5.07471C11.2163 4.86442 10.6751 4.75618 10.0938 4.75C9.66081 4.75 9.24333 4.81494 8.84131 4.94482C8.43929 5.07471 8.07129 5.25716 7.7373 5.49219C7.40332 5.72721 7.10335 6.00863 6.8374 6.33643C6.57145 6.66423 6.36426 7.03532 6.21582 7.44971C5.75195 7.23324 5.26335 7.125 4.75 7.125C4.25521 7.125 3.79443 7.21777 3.36768 7.40332C2.94092 7.58887 2.56364 7.84245 2.23584 8.16406C1.90804 8.48568 1.65137 8.86605 1.46582 9.30518C1.28027 9.7443 1.1875 10.2051 1.1875 10.6875C1.1875 11.1823 1.28027 11.6431 1.46582 12.0698C1.65137 12.4966 1.90495 12.8739 2.22656 13.2017C2.54818 13.5295 2.92546 13.7861 3.3584 13.9717C3.79134 14.1572 4.25521 14.25 4.75 14.25H15.4375Z" />
                    </svg>
                    <h1 class="text-base">Ramalan Cuaca</h1>
                </div>
                <svg id="cuaca-dropdown-icon"
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4 text-gray-400 transition-transform duration-200"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24">
                    <path d="M6 9l6 6 6-6" />
                </svg>
            </button>
            <div id="cuaca-dropdown-content"
                class="overflow-hidden max-h-0 transition-all duration-200 ease-in-out">
                <div class="ml-5 mt-2 border-l border-gray-700 pl-7 space-y-2">
                    @foreach($devices as $device_)
                    <a href="{{ route('ramalan-cuaca', ['device_id' => $device_->id, 'device_name' => $device_->device_name]) }}" class="flex gap-2 py-2 hover:text-white hover:underline">
                        <span>{{ $device_->device_name }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div>
        <hr class="my-6 border-[#333333] -m-11">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-5">
                <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                    alt="Profile picture" class="w-10 h-10 rounded-full object-cover border border-zinc-700">
                <div class="flex flex-col">
                    <span class="text-xs text-zinc-100">{{ auth()->user()->name }}</span>
                    <span class="text-[10px] text-zinc-400">{{ auth()->user()->email }}</span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="p-1.5 text-white hover:text-white rounded-md hover:bg-zinc-800 transition-colors">
                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5.83333 24.5C5.19167 24.5 4.64256 24.2717 4.186 23.8152C3.72944 23.3586 3.50078 22.8091 3.5 22.1667V5.83333C3.5 5.19167 3.72867 4.64256 4.186 4.186C4.64333 3.72944 5.19244 3.50078 5.83333 3.5H14V5.83333H5.83333V22.1667H14V24.5H5.83333ZM18.6667 19.8333L17.0625 18.1417L20.0375 15.1667H10.5V12.8333H20.0375L17.0625 9.85833L18.6667 8.16667L24.5 14L18.6667 19.8333Z" fill="#979797" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>

@once
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggles = document.querySelectorAll('.dropdown-toggle');

        toggles.forEach((btn) => {
            const contentId = btn.dataset.dropdownTarget;
            const iconId = btn.dataset.dropdownIcon;
            const content = document.getElementById(contentId);
            const icon = document.getElementById(iconId);
            const isOpen = btn.getAttribute('aria-expanded') === 'true';

            // set initial state based on aria-expanded (dashboard=open, cuaca=closed)
            if (isOpen) {
                content.style.maxHeight = content.scrollHeight + 'px';
            } else {
                content.style.maxHeight = '0px';
            }

            btn.addEventListener('click', () => {
                const expanded = btn.getAttribute('aria-expanded') === 'true';

                if (expanded) {
                    content.style.maxHeight = '0px';
                    icon.classList.remove('rotate-180');
                    btn.setAttribute('aria-expanded', 'false');
                } else {
                    content.style.maxHeight = content.scrollHeight + 'px';
                    icon.classList.add('rotate-180');
                    btn.setAttribute('aria-expanded', 'true');
                }
            });
        });
    });
</script>
@endpush
@endonce