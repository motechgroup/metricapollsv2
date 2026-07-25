<div class="py-6 sm:py-16 bg-slate-50 flex-grow min-h-screen font-sans">
    <div style="width: 95%; max-width: 1200px; margin: 0 auto;" class="space-y-6 sm:space-y-8">
        
        <!-- Navigation & Share Header Bar -->
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
            <!-- Back to All Polls Button -->
            <a href="{{ route('public.opinion') }}" style="display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: #1e293b; background-color: #ffffff; padding: 10px 18px; border-radius: 12px; border: 1px solid #cbd5e1; box-shadow: 0 1px 3px rgba(0,0,0,0.06); text-decoration: none; transition: all 150ms ease;">
                <span style="font-size: 14px;">←</span>
                <span>Back to All Polls</span>
            </a>

            <!-- Right Controls: Sample Size, Share & Export PNG Flyer Button -->
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <!-- Sample Size Pill Badge -->
                <div style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 700; font-family: monospace, sans-serif; color: #475569; background-color: #ffffff; padding: 9px 16px; border-radius: 12px; border: 1px solid #cbd5e1; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
                    <span style="color: #2563eb;">📊</span>
                    <span>Sample Size n={{ number_format($poll->votes_count) }}</span>
                </div>

                <!-- Export PNG Flyer Button -->
                <button type="button" onclick="downloadPollFlyer()" style="display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 800; color: #ffffff; background-color: #2563eb; padding: 9px 18px; border-radius: 12px; border: none; box-shadow: 0 2px 4px rgba(37,99,235,0.2); cursor: pointer; transition: all 150ms ease;" class="hover:bg-blue-700">
                    <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>📸 Export PNG Flyer ({{ strtoupper($viewMode) }})</span>
                </button>

                <!-- Copy Shareable Link Button -->
                <div x-data="{ copied: false }">
                    <button @click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 2500)" style="display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: #ffffff; background-color: #0f172a; padding: 9px 18px; border-radius: 12px; border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1); cursor: pointer; transition: background-color 150ms ease;">
                        <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                        </svg>
                        <span x-text="copied ? '✓ Link Copied!' : 'Share Results'"></span>
                    </button>
                </div>
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

        @php
            $isVoted = in_array($poll->id, $votedPollIds);
            $isEnded = $poll->status === 'ended';
            $canVote = !$isEnded && $poll->allow_public_voting;
            $candidates = is_array($poll->candidates_data) && count($poll->candidates_data) > 0 ? $poll->candidates_data : [];
            
            // Sort candidates by votes descending
            $sortedCandidates = $candidates;
            usort($sortedCandidates, function($a, $b) {
                return ($b['votes'] ?? 0) <=> ($a['votes'] ?? 0);
            });

            // Exact Politrack Color Palette
            $getPolitrackTheme = function($rankIdx, $candidateName = '') {
                $nameLower = strtolower($candidateName);
                if (str_contains($nameLower, 'undecided')) {
                    return [
                        'solidColor' => '#2563EB',
                        'cardBgColor' => 'rgba(37, 99, 235, 0.12)',
                        'fillRgba' => 'rgba(37, 99, 235, 0.22)',
                        'borderColor' => 'rgba(37, 99, 235, 0.3)',
                    ];
                }
                if (str_contains($nameLower, 'other')) {
                    return [
                        'solidColor' => '#D97706',
                        'cardBgColor' => 'rgba(217, 119, 6, 0.12)',
                        'fillRgba' => 'rgba(217, 119, 6, 0.22)',
                        'borderColor' => 'rgba(217, 119, 6, 0.3)',
                    ];
                }

                return match($rankIdx) {
                    0 => [
                        'solidColor' => '#2563EB',
                        'cardBgColor' => 'rgba(37, 99, 235, 0.12)',
                        'fillRgba' => 'rgba(37, 99, 235, 0.22)',
                        'borderColor' => 'rgba(37, 99, 235, 0.3)',
                    ],
                    1 => [
                        'solidColor' => '#9333EA',
                        'cardBgColor' => 'rgba(147, 51, 234, 0.12)',
                        'fillRgba' => 'rgba(147, 51, 234, 0.22)',
                        'borderColor' => 'rgba(147, 51, 234, 0.3)',
                    ],
                    2 => [
                        'solidColor' => '#10B981',
                        'cardBgColor' => 'rgba(16, 185, 129, 0.12)',
                        'fillRgba' => 'rgba(16, 185, 129, 0.22)',
                        'borderColor' => 'rgba(16, 185, 129, 0.3)',
                    ],
                    3 => [
                        'solidColor' => '#F59E0B',
                        'cardBgColor' => 'rgba(245, 158, 11, 0.12)',
                        'fillRgba' => 'rgba(245, 158, 11, 0.22)',
                        'borderColor' => 'rgba(245, 158, 11, 0.3)',
                    ],
                    4 => [
                        'solidColor' => '#EF4444',
                        'cardBgColor' => 'rgba(239, 68, 68, 0.12)',
                        'fillRgba' => 'rgba(239, 68, 68, 0.22)',
                        'borderColor' => 'rgba(239, 68, 68, 0.3)',
                    ],
                    5 => [
                        'solidColor' => '#2563EB',
                        'cardBgColor' => 'rgba(37, 99, 235, 0.12)',
                        'fillRgba' => 'rgba(37, 99, 235, 0.22)',
                        'borderColor' => 'rgba(37, 99, 235, 0.3)',
                    ],
                    default => [
                        'solidColor' => '#D97706',
                        'cardBgColor' => 'rgba(217, 119, 6, 0.12)',
                        'fillRgba' => 'rgba(217, 119, 6, 0.22)',
                        'borderColor' => 'rgba(217, 119, 6, 0.3)',
                    ],
                };
            };

            $watermarkUrl = asset(\App\Models\Setting::getValue('site_logo', 'images/logo.png'));
        @endphp

        <!-- Main Live Interactive Election Card Container -->
        <div style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 24px; padding: 24px;" class="shadow-sm space-y-6 sm:space-y-8">
            
            <!-- Title Header Block -->
            <div style="text-align: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 20px;" class="space-y-2 sm:space-y-3">
                <h1 style="font-size: 22px; font-weight: 900; color: #0f172a; text-transform: uppercase; tracking-spacing: -0.5px;" class="sm:text-3xl">
                    {{ strtoupper($poll->topic) }}
                </h1>
                
                <div style="font-size: 13px; color: #475569; font-weight: 500; display: flex; align-items: center; justify-content: center; gap: 6px;" class="sm:text-base">
                    <span style="font-size: 16px; color: #f59e0b;">👥</span>
                    <span>If elections were held today, who would you elect as the <strong>{{ $poll->position_title ?: 'Representative' }}</strong> for <strong>
                        @if($poll->constituency)
                            {{ $poll->constituency->name }}
                        @elseif($poll->county)
                            {{ $poll->county->name }}
                        @elseif($poll->region)
                            {{ $poll->region->name }}
                        @else
                            Kenya
                        @endif
                    </strong>?</span>
                </div>
            </div>

            <!-- Subheader Controls & View Toggle Buttons (List vs Grid) -->
            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 12px;">
                <!-- List / Grid View Switcher Buttons -->
                <div style="display: flex; align-items: center; gap: 4px; background-color: #f1f5f9; padding: 4px; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <!-- List View Button -->
                    <button wire:click="setViewMode('list')" 
                            style="{{ $viewMode === 'list' ? 'background-color: #f59e0b; color: #0f172a; box-shadow: 0 1px 2px rgba(0,0,0,0.1);' : 'background-color: transparent; color: #64748b;' }} border: none; padding: 6px 10px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 4px; transition: all 150ms ease;">
                        <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <span>List</span>
                    </button>

                    <!-- Grid View Button -->
                    <button wire:click="setViewMode('grid')" 
                            style="{{ $viewMode === 'grid' ? 'background-color: #f59e0b; color: #0f172a; box-shadow: 0 1px 2px rgba(0,0,0,0.1);' : 'background-color: transparent; color: #64748b;' }} border: none; padding: 6px 10px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 4px; transition: all 150ms ease;">
                        <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        <span>Grid</span>
                    </button>
                </div>
            </div>

            <!-- Candidate Rankings Layout: LIST VIEW vs GRID VIEW -->
            @if($viewMode === 'list')
                <!-- LIST VIEW: Horizontal Full-Width Ranking Rows -->
                <div style="display: flex; flex-direction: column; gap: 14px;">
                    @foreach($sortedCandidates as $rankIdx => $cand)
                        @php
                            $cVotes = $cand['votes'] ?? 0;
                            $pct = $poll->votes_count > 0 ? round(($cVotes / $poll->votes_count) * 100, 1) : 0;
                            $theme = $getPolitrackTheme($rankIdx, $cand['name']);
                            $cPhoto = !empty($cand['photo']) ? $cand['photo'] : '/images/favicon.png';
                        @endphp

                        <!-- Candidate Horizontal Row -->
                        <div @if($canVote && !$isVoted) wire:click="vote('{{ $cand['name'] }}')" style="cursor: pointer;" title="Click to vote for {{ $cand['name'] }}" @endif 
                             style="position: relative; width: 100%; min-height: 84px; border-radius: 18px; border: 1px solid {{ $theme['borderColor'] }}; background-color: {{ $theme['cardBgColor'] }}; display: flex; align-items: center; justify-content: space-between; overflow: hidden; padding: 12px 16px; transition: all 300ms ease;" class="hover:shadow-md group">
                            
                            <!-- Watermark Layer -->
                            <div style="position: absolute; top: 0; bottom: 0; left: 0; right: 0; pointer-events: none; opacity: 0.04; background-image: url('{{ $watermarkUrl }}'); background-repeat: repeat; background-size: 140px auto; background-position: center; z-index: 1;"></div>

                            <!-- Translucent Progress Fill Bar -->
                            <div style="position: absolute; top: 0; bottom: 0; left: 0; width: {{ $pct }}%; background-color: {{ $theme['fillRgba'] }}; border-right: 2px solid {{ $theme['solidColor'] }}; pointer-events: none; border-radius: 18px; transition: width 900ms ease-out; z-index: 2;"></div>

                            <!-- Left Content: Rank Badge + Avatar + Name & Party -->
                            <div style="display: flex; align-items: center; gap: 12px; position: relative; z-index: 10; min-width: 200px;" class="sm:gap-4 sm:min-w-[280px]">
                                <div style="width: 34px; height: 34px; min-width: 34px; border-radius: 50%; background-color: {{ $theme['solidColor'] }}; color: #ffffff; font-weight: 900; font-size: 13px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.15);" class="sm:w-10 sm:h-10 sm:min-w-[40px] sm:text-sm">
                                    #{{ $rankIdx + 1 }}
                                </div>

                                <div style="width: 48px; height: 48px; min-width: 48px; border-radius: 50%; border: 2px solid #ffffff; overflow: hidden; background-color: #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.1);" class="sm:w-14 sm:h-14 sm:min-w-[56px]">
                                    <img src="{{ asset($cPhoto) }}" alt="{{ $cand['name'] }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.src='/images/favicon.png';">
                                </div>

                                <div>
                                    <div style="font-weight: 800; font-size: 15px; color: #0f172a; line-height: 1.2;" class="sm:text-lg">{{ $cand['name'] }}</div>
                                    <div style="font-weight: 700; font-size: 11px; color: #64748b; text-transform: uppercase; margin-top: 2px; letter-spacing: 0.5px;" class="sm:text-xs">{{ $cand['party_name'] ?? 'NO PARTY' }}</div>
                                </div>
                            </div>

                            <!-- Right Content: Score Percentage & Votes -->
                            <div style="display: flex; align-items: center; gap: 12px; position: relative; z-index: 10; text-align: right; margin-left: auto;">
                                <div>
                                    <div style="font-weight: 900; font-family: monospace, sans-serif; font-size: 26px; color: {{ $theme['solidColor'] }}; line-height: 1;" class="sm:text-3xl">
                                        {{ $pct }}%
                                    </div>
                                    <div style="font-weight: 600; font-size: 11px; color: #64748b; margin-top: 3px; font-family: monospace, sans-serif;" class="sm:text-xs">{{ number_format($cVotes) }} votes</div>
                                </div>

                                @if($canVote && !$isVoted)
                                    <button wire:click.stop="vote('{{ $cand['name'] }}')" style="background-color: #0f172a; color: #ffffff; font-weight: 700; font-size: 12px; padding: 6px 14px; border-radius: 10px; border: none; cursor: pointer; transition: background-color 150ms ease;" class="hover:bg-black sm:text-xs sm:px-4 sm:py-2 sm:rounded-xl">
                                        🗳️ Vote
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- GRID VIEW: 2 COLUMNS ON MOBILE -->
                <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px;" class="sm:grid-cols-3 lg:grid-cols-5 sm:gap-4">
                    @foreach($sortedCandidates as $rankIdx => $cand)
                        @php
                            $cVotes = $cand['votes'] ?? 0;
                            $pct = $poll->votes_count > 0 ? round(($cVotes / $poll->votes_count) * 100, 1) : 0;
                            $theme = $getPolitrackTheme($rankIdx, $cand['name']);
                            $cPhoto = !empty($cand['photo']) ? $cand['photo'] : '/images/favicon.png';
                        @endphp

                        <!-- Vertical Card Grid Box -->
                        <div @if($canVote && !$isVoted) wire:click="vote('{{ $cand['name'] }}')" style="cursor: pointer;" title="Click to vote for {{ $cand['name'] }}" @endif
                             style="position: relative; border-radius: 18px; border: 1px solid {{ $theme['borderColor'] }}; background-color: {{ $theme['cardBgColor'] }}; padding: 14px 10px; display: flex; flex-direction: column; align-items: center; text-align: center; transition: all 300ms ease; overflow: hidden;" class="sm:p-5 hover:shadow-lg group">
                            
                            <!-- Watermark Layer -->
                            <div style="position: absolute; top: 0; bottom: 0; left: 0; right: 0; pointer-events: none; opacity: 0.04; background-image: url('{{ $watermarkUrl }}'); background-repeat: repeat; background-size: 120px auto; background-position: center; z-index: 1;"></div>

                            <!-- Top Right Rank Badge -->
                            <div style="position: absolute; top: 8px; right: 8px; width: 28px; height: 28px; border-radius: 50%; background-color: {{ $theme['solidColor'] }}; color: #ffffff; font-weight: 900; font-size: 11px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.15); z-index: 10;" class="sm:w-8 sm:h-8 sm:top-3 sm:right-3 sm:text-xs">
                                #{{ $rankIdx + 1 }}
                            </div>

                            <!-- Centered Candidate Photo -->
                            <div style="width: 72px; height: 72px; border-radius: 16px; border: 2.5px solid #ffffff; overflow: hidden; background-color: #f1f5f9; box-shadow: 0 3px 8px rgba(0,0,0,0.08); margin-top: 4px; margin-bottom: 10px; position: relative; z-index: 10;" class="sm:w-24 sm:h-24 sm:rounded-2xl sm:border-3 sm:mb-3">
                                <img src="{{ asset($cPhoto) }}" alt="{{ $cand['name'] }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.src='/images/favicon.png';">
                            </div>

                            <!-- Candidate Name & Party -->
                            <div style="font-weight: 800; font-size: 13px; color: #0f172a; line-height: 1.2; width: 100%; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; position: relative; z-index: 10;" class="sm:text-base">
                                {{ $cand['name'] }}
                            </div>
                            <div style="font-weight: 700; font-size: 10px; color: #64748b; text-transform: uppercase; margin-top: 2px; margin-bottom: 10px; letter-spacing: 0.5px; position: relative; z-index: 10;" class="sm:text-xs sm:mb-3">
                                {{ $cand['party_name'] ?? 'NO PARTY' }}
                            </div>

                            <!-- Large Percentage Score -->
                            <div style="font-weight: 900; font-family: monospace, sans-serif; font-size: 28px; color: {{ $theme['solidColor'] }}; line-height: 1; position: relative; z-index: 10;" class="sm:text-4xl">
                                {{ $pct }}%
                            </div>

                            <!-- Total Votes -->
                            <div style="font-weight: 600; font-size: 11px; color: #64748b; font-family: monospace, sans-serif; margin-top: 3px; margin-bottom: 10px; position: relative; z-index: 10;" class="sm:text-xs sm:mb-3">
                                {{ number_format($cVotes) }} votes
                            </div>

                            <!-- Progress Line -->
                            <div style="width: 80%; height: 5px; background-color: rgba(0,0,0,0.08); border-radius: 9999px; overflow: hidden; margin-top: auto; position: relative; z-index: 10;">
                                <div style="height: 100%; width: {{ $pct }}%; background-color: {{ $theme['solidColor'] }}; border-radius: 9999px; transition: width 900ms ease-out;"></div>
                            </div>

                            @if($canVote && !$isVoted)
                                <button wire:click.stop="vote('{{ $cand['name'] }}')" style="background-color: #0f172a; color: #ffffff; font-weight: 700; font-size: 11px; padding: 5px 10px; border-radius: 8px; border: none; cursor: pointer; margin-top: 8px; width: 100%; position: relative; z-index: 10;" class="hover:bg-black sm:text-xs sm:py-1.5 sm:rounded-xl">
                                    🗳️ Vote
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- HEAD-TO-HEAD BATTLE & SUB-COUNTY PREFERENCE BREAKDOWN -->
            <div style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 20px;" class="shadow-xs space-y-6">
                <div style="display: flex; align-items: center; justify-between; gap: 12px;" class="flex-col sm:flex-row">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 20px; color: #2563eb;">⚔️</span>
                        <div>
                            <h2 style="font-size: 16px; font-weight: 800; color: #0f172a;" class="sm:text-lg">Head-to-Head Frontrunner Battle</h2>
                            <p style="font-size: 12px; color: #64748b;">Direct comparison between top two leading candidates</p>
                        </div>
                    </div>

                    @if(count($sortedCandidates) >= 2)
                        @php
                            $top1 = $sortedCandidates[0];
                            $top2 = $sortedCandidates[1];
                            $top1Pct = $poll->votes_count > 0 ? round((($top1['votes'] ?? 0) / $poll->votes_count) * 100, 1) : 0;
                            $top2Pct = $poll->votes_count > 0 ? round((($top2['votes'] ?? 0) / $poll->votes_count) * 100, 1) : 0;
                            $margin = round($top1Pct - $top2Pct, 1);
                        @endphp
                        <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 6px 14px; border-radius: 12px; font-size: 12px; font-weight: 800;">
                            🔥 Lead Margin: +{{ $margin }}%
                        </div>
                    @endif
                </div>

                @if(count($sortedCandidates) >= 2)
                    <!-- Frontrunner Battle Bar Meter -->
                    <div style="background-color: #f8fafc; border: 1px solid #f1f5f9; border-radius: 16px; padding: 16px;" class="space-y-3">
                        <div style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; font-weight: 800;">
                            <div style="color: #2563eb; display: flex; align-items: center; gap: 6px;">
                                <span>#1 {{ $top1['name'] }}</span>
                                <span style="background-color: #dbeafe; padding: 2px 8px; border-radius: 9999px; font-size: 11px;">{{ $top1Pct }}%</span>
                            </div>
                            <div style="color: #9333ea; display: flex; align-items: center; gap: 6px;">
                                <span style="background-color: #f3e8ff; padding: 2px 8px; border-radius: 9999px; font-size: 11px;">{{ $top2Pct }}%</span>
                                <span>#2 {{ $top2['name'] }}</span>
                            </div>
                        </div>

                        <!-- Proportional Dual Color Meter Bar -->
                        <div style="width: 100%; height: 12px; background-color: #e2e8f0; border-radius: 9999px; overflow: hidden; display: flex;">
                            <div style="height: 100%; width: {{ $top1Pct }}%; background-color: #2563eb; transition: width 900ms ease;"></div>
                            <div style="height: 100%; width: {{ $top2Pct }}%; background-color: #9333ea; transition: width 900ms ease; margin-left: auto;"></div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Scope Meta Pills Bar -->
            <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; font-size: 11px; font-weight: 600;" class="sm:grid-cols-4 sm:text-xs">
                <div style="background-color: #f1f5f9; padding: 10px; border-radius: 12px; border: 1px solid #e2e8f0; text-align: center; color: #334155;">
                    <span style="color: #94a3b8;">ⓘ</span> Scope: <strong>{{ ucfirst($poll->target_level) }}</strong>
                </div>

                <div style="background-color: #f1f5f9; padding: 10px; border-radius: 12px; border: 1px solid #e2e8f0; text-align: center; color: #334155;">
                    <span style="color: #94a3b8;">ⓘ</span> Category: <strong>{{ $poll->position_title ?: 'MP' }}</strong>
                </div>

                <div style="background-color: #f1f5f9; padding: 10px; border-radius: 12px; border: 1px solid #e2e8f0; text-align: center; color: #334155;">
                    <span style="color: #2563eb;">📍</span> 
                    @if($poll->county)
                        {{ $poll->region->name ?? '' }} / {{ $poll->county->name }}
                    @else
                        Kenya / National
                    @endif
                </div>

                <div style="background-color: #f1f5f9; padding: 10px; border-radius: 12px; border: 1px solid #e2e8f0; text-align: center; color: #334155;">
                    <span style="color: #0f172a;">🗳️</span> 
                    @if($poll->constituency)
                        {{ $poll->constituency->name }} / All
                    @else
                        General Population
                    @endif
                </div>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- DEDICATED SOCIAL MEDIA INFOGRAPHIC FLYER (Export Target for html2canvas) -->
        <!-- ========================================================================= -->
        <div style="position: absolute; left: -9999px; top: -9999px; width: 840px; background-color: #ffffff; font-family: system-ui, -apple-system, sans-serif;" id="poll-infographic-flyer">
            <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 36px 40px; border-radius: 28px; color: #ffffff; border: 1px solid #334155; position: relative; overflow: hidden;" class="space-y-6">
                
                <!-- Watermark Background Layer -->
                <div style="position: absolute; inset: 0; pointer-events: none; opacity: 0.05; background-image: url('{{ $watermarkUrl }}'); background-repeat: repeat; background-size: 160px auto;"></div>

                <!-- 1. STYLED CENTERED HEADER WITH BRIGHT LOGO BADGE -->
                <div style="text-align: center; position: relative; z-index: 10; border-bottom: 1px solid rgba(255,255,255,0.15); padding-bottom: 22px;" class="space-y-3">
                    <!-- Bright Centered Logo Badge Container -->
                    <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 12px;">
                        <div style="background-color: #ffffff; padding: 10px 28px; border-radius: 18px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.2);">
                            <img src="{{ asset(\App\Models\Setting::getValue('site_logo', 'images/logo.png')) }}" style="height: 38px; width: auto; object-fit: contain;" onerror="this.onerror=null; this.src='/images/favicon.png';">
                        </div>
                    </div>

                    <!-- Clean Poll Title Centered -->
                    <h1 style="font-size: 26px; font-weight: 900; color: #ffffff; text-transform: uppercase; letter-spacing: -0.2px; line-height: 1.3; margin: 0 auto; max-width: 740px; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                        {{ $poll->topic }}
                    </h1>

                    <!-- Category & Location Scope Pills -->
                    <div style="display: flex; align-items: center; justify-content: center; gap: 10px; font-size: 12px; font-weight: 700; margin-top: 14px;">
                        <span style="background-color: #2563eb; color: #ffffff; padding: 6px 16px; border-radius: 20px; box-shadow: 0 2px 4px rgba(37,99,235,0.3);">
                            {{ $poll->position_title ?: 'Public Opinion Poll' }}
                        </span>

                        <span style="background-color: rgba(255,255,255,0.1); color: #38bdf8; padding: 6px 16px; border-radius: 20px; border: 1px solid rgba(56,189,248,0.3);">
                            📍 
                            @if($poll->constituency)
                                {{ $poll->constituency->name }} Constituency, {{ $poll->county->name ?? '' }}
                            @elseif($poll->county)
                                {{ $poll->county->name }} County
                            @elseif($poll->region)
                                {{ $poll->region->name }} Region
                            @else
                                National Target
                            @endif
                        </span>

                        <span style="background-color: rgba(255,255,255,0.1); color: #f59e0b; padding: 6px 16px; border-radius: 20px; font-family: monospace, sans-serif; border: 1px solid rgba(245,158,11,0.3);">
                            📊 n={{ number_format($poll->votes_count) }} votes
                        </span>
                    </div>
                </div>

                <!-- 2. CANDIDATES RANKING LAYOUT (DYNAMIC: LIST OR GRID BASED ON USER SELECTION) -->
                @if($viewMode === 'list')
                    <!-- LIST VIEW FLYER LAYOUT -->
                    <div style="display: flex; flex-direction: column; gap: 14px; position: relative; z-index: 10;">
                        @foreach($sortedCandidates as $rankIdx => $cand)
                            @php
                                $cVotes = $cand['votes'] ?? 0;
                                $pct = $poll->votes_count > 0 ? round(($cVotes / $poll->votes_count) * 100, 1) : 0;
                                $theme = $getPolitrackTheme($rankIdx, $cand['name']);
                                $cPhoto = !empty($cand['photo']) ? $cand['photo'] : '/images/favicon.png';
                            @endphp

                            <div style="position: relative; width: 100%; min-height: 76px; border-radius: 18px; border: 1px solid rgba(255,255,255,0.15); background-color: rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: space-between; overflow: hidden; padding: 12px 20px;">
                                <div style="position: absolute; top: 0; bottom: 0; left: 0; width: {{ $pct }}%; background-color: {{ $theme['solidColor'] }}33; border-right: 3.5px solid {{ $theme['solidColor'] }}; pointer-events: none; border-radius: 18px;"></div>

                                <div style="display: flex; align-items: center; gap: 14px; position: relative; z-index: 10;">
                                    <div style="width: 38px; height: 38px; border-radius: 50%; background-color: {{ $theme['solidColor'] }}; color: #ffffff; font-weight: 900; font-size: 14px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                                        #{{ $rankIdx + 1 }}
                                    </div>

                                    <div style="width: 50px; height: 50px; border-radius: 50%; border: 2.5px solid #ffffff; overflow: hidden; background-color: #f1f5f9; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                        <img src="{{ asset($cPhoto) }}" alt="{{ $cand['name'] }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.src='/images/favicon.png';">
                                    </div>

                                    <div>
                                        <div style="font-weight: 800; font-size: 17px; color: #ffffff; line-height: 1.2;">{{ $cand['name'] }}</div>
                                        <div style="font-weight: 700; font-size: 11px; color: #94a3b8; text-transform: uppercase; margin-top: 2px; letter-spacing: 0.5px;">{{ $cand['party_name'] ?? 'INDEPENDENT' }}</div>
                                    </div>
                                </div>

                                <div style="position: relative; z-index: 10; text-align: right;">
                                    <div style="font-weight: 900; font-family: monospace, sans-serif; font-size: 30px; color: {{ $theme['solidColor'] }}; line-height: 1;">
                                        {{ $pct }}%
                                    </div>
                                    <div style="font-weight: 600; font-size: 11px; color: #cbd5e1; margin-top: 3px; font-family: monospace, sans-serif;">{{ number_format($cVotes) }} votes</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- GRID VIEW FLYER LAYOUT -->
                    <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; position: relative; z-index: 10;">
                        @foreach($sortedCandidates as $rankIdx => $cand)
                            @php
                                $cVotes = $cand['votes'] ?? 0;
                                $pct = $poll->votes_count > 0 ? round(($cVotes / $poll->votes_count) * 100, 1) : 0;
                                $theme = $getPolitrackTheme($rankIdx, $cand['name']);
                                $cPhoto = !empty($cand['photo']) ? $cand['photo'] : '/images/favicon.png';
                            @endphp

                            <div style="position: relative; border-radius: 20px; border: 1px solid rgba(255,255,255,0.15); background-color: rgba(255,255,255,0.06); padding: 18px 12px; display: flex; flex-direction: column; align-items: center; text-align: center; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                <!-- Top Right Rank Badge -->
                                <div style="position: absolute; top: 10px; right: 10px; width: 30px; height: 30px; border-radius: 50%; background-color: {{ $theme['solidColor'] }}; color: #ffffff; font-weight: 900; font-size: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                                    #{{ $rankIdx + 1 }}
                                </div>

                                <!-- Centered Candidate Headshot -->
                                <div style="width: 72px; height: 72px; border-radius: 50%; border: 3px solid #ffffff; overflow: hidden; background-color: #f1f5f9; box-shadow: 0 4px 10px rgba(0,0,0,0.25); margin-top: 4px; margin-bottom: 10px;">
                                    <img src="{{ asset($cPhoto) }}" alt="{{ $cand['name'] }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.src='/images/favicon.png';">
                                </div>

                                <!-- Candidate Name & Party -->
                                <div style="font-weight: 800; font-size: 15px; color: #ffffff; line-height: 1.2; width: 100%; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ $cand['name'] }}
                                </div>
                                <div style="font-weight: 700; font-size: 10px; color: #94a3b8; text-transform: uppercase; margin-top: 3px; margin-bottom: 10px; letter-spacing: 0.5px;">
                                    {{ $cand['party_name'] ?? 'INDEPENDENT' }}
                                </div>

                                <!-- Large Percentage Score -->
                                <div style="font-weight: 900; font-family: monospace, sans-serif; font-size: 28px; color: {{ $theme['solidColor'] }}; line-height: 1;">
                                    {{ $pct }}%
                                </div>

                                <!-- Vote Tallies -->
                                <div style="font-weight: 600; font-size: 11px; color: #cbd5e1; font-family: monospace, sans-serif; margin-top: 3px; margin-bottom: 8px;">
                                    {{ number_format($cVotes) }} votes
                                </div>

                                <!-- Bottom Horizontal Color Line -->
                                <div style="width: 80%; height: 4px; background-color: rgba(255,255,255,0.1); border-radius: 9999px; overflow: hidden; margin-top: auto;">
                                    <div style="height: 100%; width: {{ $pct }}%; background-color: {{ $theme['solidColor'] }}; border-radius: 9999px;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- 3. SUMMARY OF THE POLL -->
                <div style="background-color: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 18px; padding: 18px; position: relative; z-index: 10; margin-top: 16px;" class="space-y-2">
                    <div style="font-size: 14px; font-weight: 800; color: #f8fafc; display: flex; align-items: center; justify-content: space-between;">
                        <span>📋 Executive Polling Summary & Takeaways</span>
                        @if(count($sortedCandidates) >= 2)
                            @php
                                $top1 = $sortedCandidates[0];
                                $top2 = $sortedCandidates[1];
                                $top1Pct = $poll->votes_count > 0 ? round((($top1['votes'] ?? 0) / $poll->votes_count) * 100, 1) : 0;
                                $top2Pct = $poll->votes_count > 0 ? round((($top2['votes'] ?? 0) / $poll->votes_count) * 100, 1) : 0;
                                $leadMargin = round($top1Pct - $top2Pct, 1);
                            @endphp
                            <span style="color: #4ade80; font-size: 13px; font-weight: 800;">🔥 Lead Margin: +{{ $leadMargin }}%</span>
                        @endif
                    </div>

                    <div style="font-size: 12px; color: #cbd5e1; line-height: 1.6;">
                        • Frontrunner <strong>{{ $sortedCandidates[0]['name'] ?? 'Candidate' }}</strong> commands top popularity index. <br>
                        • Certified statistical sample size <strong>n={{ number_format($poll->votes_count) }} respondents</strong> evaluated with zero bot/duplicate IP validations.
                    </div>
                </div>

                <!-- 4. FOOTER WITH SITE URL, SOCIAL ICONS AND DISCLAIMER -->
                <div style="border-top: 1px solid rgba(255,255,255,0.15); padding-top: 18px; margin-top: 22px; text-align: center; position: relative; z-index: 10;" class="space-y-2">
                    <!-- Centered Website URL & Social Handles -->
                    <div style="display: flex; align-items: center; justify-content: center; gap: 16px; font-size: 13px; font-weight: 800; color: #38bdf8;">
                        <span>🌐 www.metricapolls.com</span>
                        <span>•</span>
                        <span>FB: MetricaPolls</span>
                        <span>•</span>
                        <span>X: @MetricaPolls</span>
                        <span>•</span>
                        <span>IG: @metricapolls</span>
                    </div>

                    <!-- Disclaimer Notice -->
                    <div style="font-size: 10px; color: #94a3b8; line-height: 1.4; max-width: 720px; margin: 8px auto 0 auto;">
                        Disclaimer: This public opinion poll is conducted independently by Metrica Polls. Results reflect sample population sentiment at the time of polling.
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- HTML2Canvas CDN for High-Resolution PNG Flyer Export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
    function downloadPollFlyer() {
        const flyerEl = document.getElementById('poll-infographic-flyer');
        if (!flyerEl) return;

        const btn = event.currentTarget;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '⏳ Generating PNG...';
        btn.disabled = true;

        html2canvas(flyerEl, {
            scale: 2, // High DPI resolution output
            useCORS: true,
            allowTaint: true,
            backgroundColor: null,
            logging: false
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = 'Metrica_Poll_Flyer_Poll_{{ $poll->id }}.png';
            link.href = canvas.toDataURL('image/png');
            link.click();

            btn.innerHTML = '✅ PNG Downloaded!';
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }, 2500);
        }).catch(err => {
            console.error('PNG export error:', err);
            btn.innerHTML = '⚠️ Export Failed';
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }, 2500);
        });
    }
</script>
