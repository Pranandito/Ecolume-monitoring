<div id="device-list-container" class="transition-opacity duration-200 ease-in-out">

    <div class="overflow-x-auto rounded-2xl border border-white/10">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-[#171717] text-gray-400 text-sm">
                    <th class="px-6 py-4 font-medium">Id</th>
                    <th class="px-6 py-4 font-medium">Device</th>
                    <th class="px-6 py-4 font-medium">Serial Number</th>
                    <th class="px-6 py-4 font-medium">Api key</th>
                    <th class="px-6 py-4 font-medium">Konektivitas</th>
                    <th class="px-6 py-4 font-medium">Operasional</th>
                    <th class="px-6 py-4 font-medium">Owner</th>
                    <th class="px-6 py-4 font-medium">Update Terakhir</th>
                    <th class="px-6 py-4 font-medium"></th>
                </tr>
            </thead>
            <tbody id="table-body" class="text-sm text-[#979797] divide-y divide-white/10">
                @foreach($deviceList as $dev)
                <tr class="hover:bg-white/[0.03] transition">
                    <td class="px-6 py-4 whitespace-nowrap text-gray-300">{{ $dev->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $dev->device_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-300">
                        <div class="flex items-center gap-2">
                            <span>{{ $dev->serial_number }}</span>
                            <button type="button" class="btn-copy text-gray-500 hover:text-gray-200 transition" data-copy="{{ $dev->serial_number }}" title="Copy serial number">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2" />
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                                </svg>
                            </button>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap font-mono text-[#979797]">
                        <div class="flex items-center gap-2">
                            <span>{{ $dev->API_keys }}</span>
                            <button type="button" class="btn-copy text-gray-500 hover:text-gray-200 transition" data-copy="{{ $dev->API_keys }}" title="Copy API key">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2" />
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                                </svg>
                            </button>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium {{ $dev->online_status ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $dev->online_status ? 'bg-green-400' : 'bg-red-400' }}"></span>
                            {{ $dev->online_status ? 'Online' : 'Offline' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-green-500/10 text-green-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                            </svg>
                            {{ $dev->operational_status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-300">{{ $dev->user->name ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-300">{{ $dev->last_seen ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="relative inline-block text-left" data-dropdown>
                            <button type="button" onclick="toggleDropdown(this)" class="text-gray-500 hover:text-gray-300 w-8 h-8 flex items-center justify-center rounded-md hover:bg-white/5 ml-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                    <circle cx="12" cy="5" r="1.5" />
                                    <circle cx="12" cy="12" r="1.5" />
                                    <circle cx="12" cy="19" r="1.5" />
                                </svg>
                            </button>
                            <div class="hidden absolute right-0 z-10 mt-1 w-36 rounded-lg border border-white/10 bg-[#1a1a1a] shadow-lg overflow-hidden" data-menu>
                                <a href="#" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-300 hover:bg-white/5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    Lihat
                                </a>
                                <button type="button" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-400 hover:bg-white/5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6" />
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                        <line x1="10" y1="11" x2="10" y2="17" />
                                        <line x1="14" y1="11" x2="14" y2="17" />
                                    </svg>
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex items-center justify-between mt-4 px-2">
        <span></span>

        <span class="text-xs text-gray-500 self-center">
            {{ $deviceList->firstItem() }}–{{ $deviceList->lastItem() }}
            dari {{ $deviceList->total() }} Device
        </span>

        <div class="flex items-center gap-2">
            {{-- Previous --}}
            @if ($deviceList->onFirstPage())
            <button disabled class="w-8 h-8 flex items-center justify-center rounded-full border border-white/10 text-gray-600 cursor-not-allowed">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
            @else
            <a href="{{ $deviceList->previousPageUrl() }}"
                data-pagination-link
                class="w-8 h-8 flex items-center justify-center rounded-full border border-white/10 text-gray-400 hover:bg-white/5 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </a>
            @endif

            {{-- Next --}}
            @if ($deviceList->hasMorePages())
            <a href="{{ $deviceList->nextPageUrl() }}"
                data-pagination-link
                class="w-8 h-8 flex items-center justify-center rounded-full border border-white/10 text-gray-400 hover:bg-white/5 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </a>
            @else
            <button disabled class="w-8 h-8 flex items-center justify-center rounded-full border border-white/10 text-gray-600 cursor-not-allowed">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>
            @endif
        </div>
    </div>
</div>

<script>
    (function() {
        const container = document.getElementById('device-list-container');

        function bindPaginationLinks() {
            container.querySelectorAll('[data-pagination-link]').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    loadPage(this.getAttribute('href'));
                });
            });
        }

        function loadPage(url) {
            if (!url) return;

            container.classList.add('opacity-0');

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContainer = doc.getElementById('device-list-container');

                    setTimeout(() => {
                        if (newContainer) {
                            container.innerHTML = newContainer.innerHTML;
                            bindPaginationLinks(); // rebind karena innerHTML diganti
                        }
                        container.classList.remove('opacity-0');
                    }, 150); // samakan dengan durasi transition
                })
                .catch(err => {
                    console.error('Gagal load halaman:', err);
                    container.classList.remove('opacity-0');
                });
        }

        bindPaginationLinks();
    })();
</script>