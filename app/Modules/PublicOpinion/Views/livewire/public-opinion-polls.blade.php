<div class="py-12 sm:py-20 bg-slate-50 flex-grow min-h-screen font-sans">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header Banner with Self-Contained Styles for Maximum Readability -->
        <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #090d16 100%); color: #ffffff; padding: 36px 32px; border-radius: 24px; box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.3);" class="relative overflow-hidden">
            <div style="position: absolute; right: -40px; bottom: -40px; opacity: 0.1; width: 384px; height: 384px; border-radius: 50%; background-color: #ffffff; filter: blur(48px); pointer-events: none;"></div>
            
            <div style="position: relative; z-index: 10; max-width: 800px;" class="space-y-3">
                <span style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 9999px; font-size: 12px; font-weight: 700; background-color: rgba(255,255,255,0.1); color: #38bdf8; border: 1px solid rgba(255,255,255,0.15); backdrop-filter: blur(4px);">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #34d399;" class="animate-ping"></span> Real-Time Public Intelligence & Audit
                </span>
                
                <h1 style="font-size: 30px; font-weight: 900; color: #ffffff; letter-spacing: -0.5px; margin-top: 6px;" class="sm:text-4xl">
                    Public Opinion Polls & Results
                </h1>
                
                <p style="font-size: 15px; color: #cbd5e1; line-height: 1.6; font-weight: 400; margin-top: 6px;" class="sm:text-base">
                    Explore verified electoral popularity index and opinion poll findings across Kenya’s 8 Regions, 47 Counties, and Constituencies for positions such as Woman Representative, Governor, Senator, and President.
                </p>
            </div>
        </div>

        @if (session()->has('success'))
            <div style="padding: 14px 18px; background-color: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; font-size: 14px; font-weight: 600; border-radius: 16px; display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 18px;">✅</span> {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div style="padding: 14px 18px; background-color: #fef2f2; border: 1px solid #fecaca; color: #991b1b; font-size: 14px; font-weight: 600; border-radius: 16px; display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 18px;">⚠️</span> {{ session('error') }}
            </div>
        @endif

        <!-- Filter & Search Bar Panel -->
        <div style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 24px;" class="shadow-sm space-y-4">
            <!-- Status Tabs -->
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 16px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <button wire:click="$set('statusFilter', 'all')" 
                            style="{{ $statusFilter === 'all' ? 'background-color: #0f172a; color: #ffffff;' : 'background-color: #f1f5f9; color: #475569;' }} border: none; padding: 8px 16px; border-radius: 10px; font-size: 12px; font-weight: 800; cursor: pointer; transition: all 150ms ease;">
                        All Results
                    </button>
                    <button wire:click="$set('statusFilter', 'live')" 
                            style="{{ $statusFilter === 'live' ? 'background-color: #059669; color: #ffffff;' : 'background-color: #f1f5f9; color: #475569;' }} border: none; padding: 8px 16px; border-radius: 10px; font-size: 12px; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all 150ms ease;">
                        <span style="width: 7px; height: 7px; border-radius: 50%; background-color: #6ee7b7;" class="animate-pulse"></span> Live Index
                    </button>
                    <button wire:click="$set('statusFilter', 'ended')" 
                            style="{{ $statusFilter === 'ended' ? 'background-color: #0f172a; color: #ffffff;' : 'background-color: #f1f5f9; color: #475569;' }} border: none; padding: 8px 16px; border-radius: 10px; font-size: 12px; font-weight: 800; cursor: pointer; transition: all 150ms ease;">
                        🔴 Certified Final Results
                    </button>
                </div>

                <div class="w-full sm:w-auto">
                    <input type="text" wire:model.live="search" placeholder="Search topic, candidate or region..." style="border: 1px solid #cbd5e1; padding: 8px 14px; border-radius: 10px; font-size: 12px; color: #0f172a; font-weight: 600;" class="w-full sm:w-72 focus:outline-none focus:border-blue-600">
                </div>
            </div>

            <!-- Geographic & Position Filter Dropdowns -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 pt-1">
                <div>
                    <label style="display: block; font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">REGION</label>
                    <select wire:model.live="selectedRegion" style="width: 100%; border: 1px solid #cbd5e1; padding: 8px 12px; border-radius: 10px; font-size: 12px; font-weight: 700; color: #0f172a; background-color: #ffffff;">
                        <option value="">All Regions (8)</option>
                        @foreach($regions as $reg)
                            <option value="{{ $reg->id }}">{{ $reg->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">COUNTY</label>
                    <select wire:model.live="selectedCounty" style="width: 100%; border: 1px solid #cbd5e1; padding: 8px 12px; border-radius: 10px; font-size: 12px; font-weight: 700; color: #0f172a; background-color: #ffffff;">
                        <option value="">All Counties (47)</option>
                        @foreach($counties as $co)
                            <option value="{{ $co->id }}">{{ $co->name }} (Code {{ $co->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">CONSTITUENCY</label>
                    <select wire:model.live="selectedConstituency" style="width: 100%; border: 1px solid #cbd5e1; padding: 8px 12px; border-radius: 10px; font-size: 12px; font-weight: 700; color: #0f172a; background-color: #ffffff;" {{ empty($selectedCounty) ? 'disabled' : '' }}>
                        <option value="">All Constituencies</option>
                        @foreach($constituencies as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">POSITION / OFFICE</label>
                    <select wire:model.live="selectedPosition" style="width: 100%; border: 1px solid #cbd5e1; padding: 8px 12px; border-radius: 10px; font-size: 12px; font-weight: 700; color: #0f172a; background-color: #ffffff;">
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
                <div style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 22px; display: flex; flex-direction: column; justify-content: space-between; gap: 18px;" class="shadow-xs hover:shadow-md transition group">
                    <div class="space-y-3">
                        <!-- Top Header: Status Badge & Location -->
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                            <div>
                                @if($poll->status === 'live')
                                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 3px 10px; border-radius: 9999px; font-size: 11px; font-weight: 800; background-color: #d1fae5; color: #065f46;">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background-color: #059669;" class="animate-pulse"></span> LIVE
                                    </span>
                                @else
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 9999px; font-size: 11px; font-weight: 800; background-color: #0f172a; color: #ffffff;">
                                        🔴 ENDED
                                    </span>
                                @endif
                            </div>

                            <span style="font-size: 12px; font-weight: 700; color: #2563eb; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px;">
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
                            <h2 style="font-size: 16px; font-weight: 800; color: #0f172a; line-height: 1.4;" class="group-hover:text-blue-600 transition">
                                {{ $poll->topic }}
                            </h2>
                        </a>

                        <!-- Position Title Badge -->
                        @if($poll->position_title)
                            <div style="padding-top: 4px;">
                                <span style="display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 8px; font-size: 11px; font-weight: 700; background-color: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">
                                    Target Position: {{ $poll->position_title }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Meta Specs & Action Button -->
                    <div style="padding-top: 14px; border-top: 1px solid #f1f5f9;" class="space-y-4">
                        <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; font-size: 12px;">
                            <div>
                                <span style="font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; display: block;">End Date</span>
                                <span style="font-weight: 800; color: #334155;">
                                    {{ $poll->expires_at ? $poll->expires_at->format('M d, Y') : 'Ongoing' }}
                                </span>
                            </div>
                            <div style="text-align: right;">
                                <span style="font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; display: block;">Total Votes</span>
                                <span style="font-weight: 900; color: #0f172a; font-family: monospace, sans-serif;">
                                    {{ number_format($poll->votes_count) }}
                                </span>
                            </div>
                        </div>

                        <a href="{{ route('public.opinion.show', $poll->id) }}" style="width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 8px; border-radius: 12px; background-color: #0f172a; color: #ffffff; font-weight: 800; font-size: 12px; padding: 10px 16px; text-decoration: none; transition: background-color 150ms ease;" class="hover:bg-blue-600">
                            <span>View Poll Results</span>
                            <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @empty
                <div style="grid-column: span 3; background-color: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; padding: 48px; text-align: center; color: #64748b;" class="space-y-3">
                    <div style="font-size: 36px;">📊</div>
                    <h3 style="font-size: 16px; font-weight: 800; color: #0f172a;">No poll results found</h3>
                    <p style="font-size: 12px; color: #64748b;">Try adjusting your region, county, or status filter.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
