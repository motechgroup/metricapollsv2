<div class="py-12 sm:py-20 bg-gray-50 flex-grow min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-brand-navy via-blue-900 to-indigo-950 rounded-2xl p-8 text-white shadow-xl relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 opacity-10 w-96 h-96 rounded-full bg-white blur-3xl pointer-events-none"></div>
            <div class="max-w-3xl space-y-3 relative z-10">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/10 text-brand-teal backdrop-blur border border-white/10">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span> Real-Time Public Intelligence & Audit
                </span>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight font-sans text-white">Public Opinion Polls & Results</h1>
                <p class="text-base text-gray-200 leading-relaxed">
                    Explore verified electoral popularity index and opinion poll findings across Kenya’s 8 Regions, 47 Counties, and Constituencies for positions such as Woman Representative, Governor, Senator, and President.
                </p>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold rounded-xl flex items-center gap-3">
                <span class="text-xl">✅</span> {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="p-4 bg-red-50 border border-red-200 text-red-700 text-sm font-semibold rounded-xl flex items-center gap-3">
                <span class="text-xl">⚠️</span> {{ session('error') }}
            </div>
        @endif

        <!-- Filter & Search Bar Panel -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-4">
            <!-- Status Tabs -->
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-100 pb-4">
                <div class="flex items-center gap-2">
                    <button wire:click="$set('statusFilter', 'all')" class="px-4 py-2 rounded-lg text-xs font-bold transition {{ $statusFilter === 'all' ? 'bg-brand-navy text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        All Results
                    </button>
                    <button wire:click="$set('statusFilter', 'live')" class="px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ $statusFilter === 'live' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span> Live Index
                    </button>
                    <button wire:click="$set('statusFilter', 'ended')" class="px-4 py-2 rounded-lg text-xs font-bold transition {{ $statusFilter === 'ended' ? 'bg-gray-900 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        🔴 Certified Final Results
                    </button>
                </div>

                <div class="w-full sm:w-auto">
                    <input type="text" wire:model.live="search" placeholder="Search topic, candidate or region..." class="w-full sm:w-72 rounded-lg border border-gray-300 px-3.5 py-2 text-xs focus:ring-brand-blue focus:border-brand-blue">
                </div>
            </div>

            <!-- Geographic & Position Filter Dropdowns -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 pt-1">
                <div>
                    <label class="block text-xxs font-bold text-gray-500 uppercase tracking-wider mb-1">Region</label>
                    <select wire:model.live="selectedRegion" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs focus:ring-brand-blue focus:border-brand-blue">
                        <option value="">All Regions (8)</option>
                        @foreach($regions as $reg)
                            <option value="{{ $reg->id }}">{{ $reg->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xxs font-bold text-gray-500 uppercase tracking-wider mb-1">County</label>
                    <select wire:model.live="selectedCounty" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs focus:ring-brand-blue focus:border-brand-blue">
                        <option value="">All Counties (47)</option>
                        @foreach($counties as $co)
                            <option value="{{ $co->id }}">{{ $co->name }} (Code {{ $co->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xxs font-bold text-gray-500 uppercase tracking-wider mb-1">Constituency</label>
                    <select wire:model.live="selectedConstituency" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs focus:ring-brand-blue focus:border-brand-blue" {{ empty($selectedCounty) ? 'disabled' : '' }}>
                        <option value="">All Constituencies</option>
                        @foreach($constituencies as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xxs font-bold text-gray-500 uppercase tracking-wider mb-1">Position / Office</label>
                    <select wire:model.live="selectedPosition" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs focus:ring-brand-blue focus:border-brand-blue">
                        <option value="">All Positions</option>
                        <option value="Woman Representative">Woman Representative</option>
                        <option value="Governor">Governor</option>
                        <option value="Senator">Senator</option>
                        <option value="President">President</option>
                        <option value="Member of Parliament">Member of Parliament (MP)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Poll Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($polls as $poll)
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between space-y-5 relative overflow-hidden group">
                    <div class="space-y-3">
                        <!-- Top Header: Status Badge & Location -->
                        <div class="flex items-center justify-between gap-2 border-b border-gray-100 pb-3">
                            <div>
                                @if($poll->status === 'live')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xxs font-bold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span> LIVE
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xxs font-bold bg-gray-900 text-white">
                                        🔴 ENDED
                                    </span>
                                @endif
                            </div>

                            <span class="text-xs text-brand-blue font-bold truncate">
                                📍 
                                @if($poll->constituency)
                                    {{ $poll->constituency->name }}, {{ $poll->county->name ?? '' }}
                                @elseif($poll->county)
                                    {{ $poll->county->name }} County
                                @elseif($poll->region)
                                    {{ $poll->region->name }} Region
                                @else
                                    National
                                @endif
                            </span>
                        </div>

                        <!-- Poll Title -->
                        <a href="{{ route('public.opinion.show', $poll->id) }}" class="block">
                            <h2 class="text-base font-bold text-gray-950 leading-snug group-hover:text-brand-navy transition line-clamp-2">
                                {{ $poll->topic }}
                            </h2>
                        </a>

                        <!-- Position Title Badge if set -->
                        @if($poll->position_title)
                            <div class="pt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xxs font-bold bg-blue-50 text-brand-blue border border-blue-100">
                                    Target Position: {{ $poll->position_title }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Meta Specs & Action Button -->
                    <div class="space-y-4 pt-3 border-t border-gray-100">
                        <div class="grid grid-cols-2 gap-2 text-xs text-gray-500">
                            <div>
                                <span class="text-xxs font-semibold text-gray-400 block uppercase">End Date</span>
                                <span class="font-bold text-gray-800">
                                    {{ $poll->expires_at ? $poll->expires_at->format('M d, Y') : 'Ongoing' }}
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-xxs font-semibold text-gray-400 block uppercase">Total Votes</span>
                                <span class="font-bold text-gray-900 font-mono">
                                    {{ number_format($poll->votes_count) }}
                                </span>
                            </div>
                        </div>

                        <a href="{{ route('public.opinion.show', $poll->id) }}" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-brand-navy hover:bg-brand-navy/90 text-white font-bold text-xs py-2.5 px-4 transition shadow-sm">
                            <span>View Poll Results</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 bg-white rounded-2xl border border-gray-200 p-12 text-center text-gray-500 space-y-3">
                    <div class="text-3xl">📊</div>
                    <h3 class="text-base font-bold text-gray-900">No poll results found</h3>
                    <p class="text-xs text-gray-500">Try adjusting your region, county, or status filter.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
