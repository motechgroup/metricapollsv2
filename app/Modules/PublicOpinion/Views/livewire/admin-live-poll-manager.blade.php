@section('page_title', 'Live & Political Polls Manager')

<div class="space-y-6 font-sans w-full">
    <!-- Top Header Bar with Prominent Create New Poll Button -->
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 20px;" class="shadow-sm">
        <div>
            <h1 style="font-size: 24px; font-weight: 800; color: #0f172a;">Live & Ended Public Polls Manager</h1>
            <p style="font-size: 13px; color: #64748b; margin-top: 2px;">Manage public opinion polls in a clean full-width table list. Click any row to expand details, edit votes, or pick candidate photos from Media Gallery.</p>
        </div>

        <div style="display: flex; align-items: center; gap: 10px;">
            <button wire:click="openMediaGallery" style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 700; color: #0f172a; background-color: #f1f5f9; padding: 11px 16px; border-radius: 12px; border: 1px solid #cbd5e1; cursor: pointer; transition: all 150ms ease;">
                <span style="font-size: 15px;">🖼️</span>
                <span>Media Gallery</span>
            </button>

            <button wire:click="openCreateModal" style="display: inline-flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 800; color: #ffffff; background-color: #0f172a; padding: 12px 20px; border-radius: 12px; border: none; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); transition: all 150ms ease;">
                <span style="font-size: 16px;">➕</span>
                <span>Create New Poll</span>
            </button>
        </div>
    </div>

    @if(session()->has('success'))
        <div style="padding: 14px 18px; background-color: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; font-size: 14px; font-weight: 600; border-radius: 14px; display: flex; align-items: center; justify-content: space-between;">
            <div>✅ {{ session('success') }}</div>
        </div>
    @endif

    @if(session()->has('error'))
        <div style="padding: 14px 18px; background-color: #fef2f2; border: 1px solid #fecaca; color: #991b1b; font-size: 14px; font-weight: 600; border-radius: 14px;">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    <!-- Managed Polls Table / List View Panel (Full Width 100%) -->
    <div class="space-y-4 w-full">
        <!-- Filter, Search, and PerPage Controls Bar -->
        <div style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 16px;" class="shadow-sm space-y-3">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <!-- Status Filter Tabs -->
                <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                    <span style="font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase;">Status:</span>
                    <button wire:click="$set('filterStatus', 'all')" style="{{ $filterStatus === 'all' ? 'background-color: #0f172a; color: #ffffff;' : 'background-color: #f1f5f9; color: #475569;' }} border: none; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;">
                        All ({{ \App\Modules\PublicOpinion\Models\PublicOpinion::count() }})
                    </button>
                    <button wire:click="$set('filterStatus', 'live')" style="{{ $filterStatus === 'live' ? 'background-color: #059669; color: #ffffff;' : 'background-color: #ecfdf5; color: #047857;' }} border: 1px solid #a7f3d0; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;">
                        🟢 Live
                    </button>
                    <button wire:click="$set('filterStatus', 'ended')" style="{{ $filterStatus === 'ended' ? 'background-color: #0f172a; color: #ffffff;' : 'background-color: #f1f5f9; color: #475569;' }} border: none; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;">
                        🔴 Ended
                    </button>
                </div>

                <!-- Target Level & County Filters -->
                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                    <select wire:model.live="filterTargetLevel" style="border-radius: 10px; border: 1px solid #cbd5e1; padding: 7px 12px; font-size: 12px; background-color: #ffffff;">
                        <option value="all">All Target Levels</option>
                        <option value="national">National</option>
                        <option value="region">Regional</option>
                        <option value="county">County</option>
                        <option value="constituency">Constituency</option>
                    </select>

                    <select wire:model.live="filterCountyId" style="border-radius: 10px; border: 1px solid #cbd5e1; padding: 7px 12px; font-size: 12px; background-color: #ffffff;">
                        <option value="">All Counties</option>
                        @foreach($counties as $co)
                            <option value="{{ $co->id }}">{{ $co->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Search Input & Per-Page Controls -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2 border-t border-gray-100">
                <div class="w-full sm:flex-1">
                    <input type="text" wire:model.live.debounce.250ms="searchQuery" placeholder="🔍 Search polls by topic title or position title..." style="width: 100%; border-radius: 10px; border: 1px solid #cbd5e1; padding: 8px 14px; font-size: 13px; background-color: #ffffff;">
                </div>

                <div style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: #64748b;">
                    <span>Show per page:</span>
                    <select wire:model.live="perPage" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 6px 10px; font-size: 12px; background-color: #ffffff;">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Clean Full-Width Data Table List Format -->
        <div style="background-color: #ffffff; border: 1px solid #cbd5e1; border-radius: 20px; overflow: hidden;" class="shadow-xs w-full">
            <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
                <thead>
                    <tr style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">
                        <th style="padding: 14px 20px; width: 90px;">Status</th>
                        <th style="padding: 14px 20px;">Poll Topic & Location Target</th>
                        <th style="padding: 14px 20px; width: 160px;">Position / Office</th>
                        <th style="padding: 14px 20px; width: 110px; text-align: right;">Total Votes</th>
                        <th style="padding: 14px 20px; width: 120px; text-align: center;">Visitor Voting</th>
                        <th style="padding: 14px 20px; width: 220px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody style="divide-y: 1px solid #f1f5f9;">
                    @forelse($polls as $poll)
                        <!-- Main Compact List Row (Click to toggle candidate details drawer) -->
                        <tr style="border-bottom: 1px solid #f1f5f9; cursor: pointer; transition: background-color 150ms ease;" class="hover:bg-slate-50/80" wire:click="toggleExpandPoll({{ $poll->id }})">
                            <td style="padding: 14px 20px; vertical-align: middle;">
                                <span style="font-weight: 800; font-family: monospace; color: #94a3b8; display: block; font-size: 11px;">#{{ $poll->id }}</span>
                                @if($poll->status === 'live')
                                    <span style="display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 10px; font-weight: 800; background-color: #d1fae5; color: #065f46;">🟢 LIVE</span>
                                @else
                                    <span style="display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 10px; font-weight: 800; background-color: #f1f5f9; color: #475569;">🔴 ENDED</span>
                                @endif
                            </td>

                            <td style="padding: 14px 20px; vertical-align: middle;">
                                <div style="font-weight: 800; color: #0f172a; font-size: 15px; line-height: 1.3;">{{ $poll->topic }}</div>
                                <div style="display: flex; align-items: center; gap: 8px; margin-top: 4px;">
                                    <span style="font-size: 11px; font-weight: 700; color: #2563eb; background-color: #eff6ff; padding: 2px 8px; border-radius: 6px; border: 1px solid #bfdbfe;">
                                        📍 
                                        @if($poll->constituency)
                                            {{ $poll->constituency->name }} Constituency
                                        @elseif($poll->county)
                                            {{ $poll->county->name }} County
                                        @elseif($poll->region)
                                            {{ $poll->region->name }} Region
                                        @else
                                            National
                                        @endif
                                    </span>
                                    <span style="font-size: 11px; color: #64748b; font-weight: 600;">
                                        {{ $expandedPollId === $poll->id ? '▲ Hide Details' : '▼ Click row to edit candidate photos & votes (' . (is_array($poll->candidates_data) ? count($poll->candidates_data) : 0) . ')' }}
                                    </span>
                                </div>
                            </td>

                            <td style="padding: 14px 20px; vertical-align: middle; font-weight: 700; color: #334155; font-size: 13px;">
                                {{ $poll->position_title ?: 'General' }}
                            </td>

                            <td style="padding: 14px 20px; vertical-align: middle; font-weight: 800; font-family: monospace; color: #0f172a; text-align: right; font-size: 14px;">
                                {{ number_format($poll->votes_count) }}
                            </td>

                            <td style="padding: 14px 20px; vertical-align: middle; text-align: center;" @click.stop>
                                <button wire:click="togglePublicVoting({{ $poll->id }})" style="font-size: 11px; font-weight: 700; {{ $poll->allow_public_voting ? 'background-color: #f3e8ff; color: #6b21a8; border: 1px solid #e9d5ff;' : 'background-color: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;' }} padding: 4px 10px; border-radius: 6px; cursor: pointer;">
                                    {{ $poll->allow_public_voting ? '🗳️ OPEN' : '🔒 OFF' }}
                                </button>
                            </td>

                            <td style="padding: 14px 20px; vertical-align: middle; text-align: right;" @click.stop>
                                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 6px;">
                                    <a href="{{ route('public.opinion.show', $poll->id) }}" target="_blank" style="font-size: 11px; font-weight: 700; color: #2563eb; background-color: #eff6ff; padding: 5px 10px; border-radius: 6px; border: 1px solid #bfdbfe; text-decoration: none;">
                                        👁️ View
                                    </a>
                                    <button wire:click="openEditModal({{ $poll->id }})" style="font-size: 11px; font-weight: 700; color: #92400e; background-color: #fef3c7; padding: 5px 10px; border-radius: 6px; border: 1px solid #fde68a; cursor: pointer;">
                                        ✏️ Edit
                                    </button>
                                    <button wire:click="togglePollStatus({{ $poll->id }})" style="font-size: 11px; font-weight: 700; color: #334155; background-color: #f1f5f9; padding: 5px 10px; border-radius: 6px; border: 1px solid #cbd5e1; cursor: pointer;">
                                        Toggle
                                    </button>
                                    <button wire:click="deletePoll({{ $poll->id }})" wire:confirm="Are you sure you want to delete this poll?" style="font-size: 11px; font-weight: 700; color: #991b1b; background-color: #fef2f2; padding: 5px 10px; border-radius: 6px; border: 1px solid #fecaca; cursor: pointer;">
                                        ✕
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Expanded Candidate Details Row with Candidate Photo & Vote Editors -->
                        @if($expandedPollId === $poll->id)
                            <tr style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                                <td colspan="6" style="padding: 20px;">
                                    <div style="background-color: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 18px;" class="space-y-4">
                                        <div style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; font-weight: 800; color: #0f172a;">
                                            <span>📊 Candidate Breakdown, Photo Editor & Live Vote Controllers for Poll #{{ $poll->id }}</span>
                                            <button wire:click="toggleExpandPoll({{ $poll->id }})" style="font-size: 11px; color: #64748b; background: none; border: none; cursor: pointer; font-weight: 700;">Close Details ✕</button>
                                        </div>

                                        @if(is_array($poll->candidates_data) && count($poll->candidates_data) > 0)
                                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 14px; padding-top: 4px;">
                                                @foreach($poll->candidates_data as $candIdx => $cand)
                                                    @php
                                                        $cnt = $cand['votes'] ?? 0;
                                                        $pct = $poll->votes_count > 0 ? round(($cnt / $poll->votes_count) * 100, 1) : 0;
                                                        $photoPath = !empty($cand['photo']) ? $cand['photo'] : '/images/favicon.png';
                                                    @endphp
                                                    <div style="background-color: #f8fafc; padding: 14px; border-radius: 14px; border: 1px solid #cbd5e1; display: flex; flex-direction: column; gap: 10px;" x-data="{ editVal: {{ $cnt }}, editPhoto: '{{ $photoPath }}' }">
                                                        <!-- Top Row: Photo, Candidate Info & Votes Editor -->
                                                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px;">
                                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                                <img :src="editPhoto || '/images/favicon.png'" onerror="this.onerror=null; this.src='/images/favicon.png';" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                                                <div>
                                                                    <div style="font-weight: 800; font-size: 13px; color: #0f172a;">{{ $cand['name'] }}</div>
                                                                    <div style="font-size: 11px; color: #64748b; font-family: monospace;">{{ $pct }}% ({{ number_format($cnt) }} votes)</div>
                                                                </div>
                                                            </div>

                                                            <div style="display: flex; align-items: center; gap: 6px;">
                                                                <input type="number" x-model="editVal" style="width: 75px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 5px 8px; font-size: 12px; font-family: monospace; text-align: right; background-color: #ffffff;">
                                                                <button type="button" @click="$wire.updateCandidateVotes({{ $poll->id }}, {{ $candIdx }}, editVal)" style="background-color: #0f172a; color: #ffffff; font-weight: 700; font-size: 11px; padding: 5px 10px; border-radius: 8px; border: none; cursor: pointer;">
                                                                    Update
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <!-- Bottom Row: Candidate Photo URL Editor & Media Gallery Trigger -->
                                                        <div style="display: flex; align-items: center; gap: 6px; padding-top: 8px; border-top: 1px solid #e2e8f0; flex-wrap: wrap;">
                                                            <input type="text" x-model="editPhoto" placeholder="/images/photo.jpg or https://..." style="flex: 1; min-width: 140px; border-radius: 6px; border: 1px solid #cbd5e1; padding: 4px 8px; font-size: 11px; background-color: #ffffff;">
                                                            <button type="button" @click="$wire.updateCandidatePhoto({{ $poll->id }}, {{ $candIdx }}, editPhoto)" style="background-color: #059669; color: #ffffff; font-weight: 700; font-size: 10px; padding: 4px 8px; border-radius: 6px; border: none; cursor: pointer;">
                                                                💾 Save
                                                            </button>
                                                            <button type="button" wire:click="openMediaGallery('drawer', {{ $poll->id }}, {{ $candIdx }})" style="background-color: #2563eb; color: #ffffff; font-weight: 700; font-size: 10px; padding: 4px 8px; border-radius: 6px; border: none; cursor: pointer;">
                                                                🖼️ Gallery
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 40px; text-align: center; color: #64748b; font-size: 14px;">
                                No polls found matching the specified filters or search query.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Livewire Pagination Links Bar -->
            @if($polls->hasPages())
                <div style="padding: 16px 20px; background-color: #f8fafc; border-top: 1px solid #e2e8f0;">
                    {{ $polls->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Media Library & Gallery Picker Modal Overlay -->
    @if($showMediaGalleryModal)
        <div style="position: fixed; inset: 0; background-color: rgba(15, 23, 42, 0.75); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 10000; padding: 16px; overflow-y: auto;">
            <div style="background-color: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; width: 100%; max-width: 800px; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);" class="font-sans">
                
                <!-- Modal Header -->
                <div style="padding: 18px 24px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; background-color: #f8fafc; border-top-left-radius: 20px; border-top-right-radius: 20px;">
                    <div>
                        <h3 style="font-size: 18px; font-weight: 800; color: #0f172a;">🖼️ System Media Manager & Photo Gallery</h3>
                        <p style="font-size: 12px; color: #64748b; margin-top: 2px;">Upload new candidate headshots from local drive or pick saved photos for future polls.</p>
                    </div>
                    <button wire:click="closeMediaGallery" style="background-color: #e2e8f0; border: none; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #475569; font-weight: 700; cursor: pointer;">
                        ✕
                    </button>
                </div>

                <!-- Modal Body -->
                <div style="padding: 24px; overflow-y: auto; flex: 1;" class="space-y-6">
                    <!-- Local File Upload Box -->
                    <div style="background-color: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 16px; padding: 20px; text-align: center;">
                        <form wire:submit.prevent="uploadNewMediaAsset" class="space-y-3">
                            <div style="font-size: 13px; font-weight: 800; color: #0f172a;">📁 Upload Local Candidate Image / Logo</div>
                            <div style="font-size: 11px; color: #64748b;">Select JPG, PNG, or WEBP file from your device (Max 5MB)</div>

                            <div style="display: flex; align-items: center; justify-content: center; gap: 12px;">
                                <input type="file" wire:model="uploadedImage" accept="image/*" style="font-size: 12px; color: #475569;">
                                @if($uploadedImage)
                                    <button type="submit" style="background-color: #0f172a; color: #ffffff; font-weight: 700; font-size: 12px; padding: 8px 16px; border-radius: 10px; border: none; cursor: pointer;">
                                        ⬆️ Upload to Gallery
                                    </button>
                                @endif
                            </div>

                            @error('uploadedImage') <span style="display: block; font-size: 11px; color: #ef4444;">{{ $message }}</span> @enderror

                            <div wire:loading wire:target="uploadedImage" style="font-size: 11px; color: #2563eb; font-weight: 700;">
                                ⏳ Processing image upload...
                            </div>
                        </form>
                    </div>

                    <!-- Gallery Search Input -->
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                        <h4 style="font-size: 14px; font-weight: 800; color: #0f172a;">Saved Media Library ({{ count($mediaAssets) }} Assets)</h4>
                        <input type="text" wire:model.live.debounce.250ms="mediaSearch" placeholder="🔍 Search gallery by candidate name or image path..." style="width: 280px; border-radius: 10px; border: 1px solid #cbd5e1; padding: 7px 12px; font-size: 12px; background-color: #ffffff;">
                    </div>

                    <!-- Image Grid Gallery -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 12px; max-height: 340px; overflow-y: auto; padding: 4px;">
                        @forelse($mediaAssets as $asset)
                            <div wire:click="selectMediaAsset('{{ $asset->file_path }}')" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 8px; text-align: center; cursor: pointer; transition: all 150ms ease;" class="hover:border-blue-500 hover:shadow-md group">
                                <div style="width: 100%; height: 90px; border-radius: 8px; overflow: hidden; background-color: #f1f5f9; display: flex; align-items: center; justify-content: center; margin-bottom: 6px;">
                                    <img src="{{ asset($asset->file_path ?: '/images/favicon.png') }}" onerror="this.onerror=null; this.src='/images/favicon.png';" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <div style="font-size: 11px; font-weight: 800; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $asset->name }}">
                                    {{ $asset->name }}
                                </div>
                                <div style="font-size: 9px; font-weight: 700; color: #2563eb; margin-top: 2px;">
                                    Select Image ↗
                                </div>
                            </div>
                        @empty
                            <div style="grid-column: 1 / -1; padding: 24px; text-align: center; color: #64748b; font-size: 13px;">
                                No images found in gallery. Use the local file upload box above to add candidate photos!
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Modal Footer -->
                <div style="padding: 14px 24px; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: flex-end; background-color: #f8fafc; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
                    <button type="button" wire:click="closeMediaGallery" style="background-color: #ffffff; border: 1px solid #cbd5e1; color: #475569; font-weight: 700; font-size: 13px; padding: 8px 16px; border-radius: 10px; cursor: pointer;">
                        Close Gallery
                    </button>
                </div>

            </div>
        </div>
    @endif

    <!-- Create New Poll Pop-Up Modal Window -->
    @if($showCreateModal)
        <div style="position: fixed; inset: 0; background-color: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 16px; overflow-y: auto;">
            <div style="background-color: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; width: 100%; max-width: 680px; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);" class="font-sans">
                
                <!-- Modal Header -->
                <div style="padding: 18px 24px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; background-color: #f8fafc; border-top-left-radius: 20px; border-top-right-radius: 20px;">
                    <div>
                        <h3 style="font-size: 18px; font-weight: 800; color: #0f172a;">➕ Create & Publish New Poll</h3>
                        <p style="font-size: 12px; color: #64748b; margin-top: 2px;">Fill in poll details, set geographic level, and assign candidate photos & votes</p>
                    </div>
                    <button wire:click="closeCreateModal" style="background-color: #e2e8f0; border: none; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #475569; font-weight: 700; cursor: pointer;">
                        ✕
                    </button>
                </div>

                <!-- Modal Form Body -->
                <form wire:submit.prevent="createPoll" style="display: flex; flex-direction: column; flex: 1; overflow: hidden;">
                    <div style="padding: 24px; overflow-y: auto; flex: 1;" class="space-y-4">
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Poll Topic / Title *</label>
                            <input type="text" wire:model="topic" placeholder="e.g. 2026 Preferred Woman Representative for Kisii County" style="width: 100%; border-radius: 10px; border: 1px solid #cbd5e1; padding: 9px 14px; font-size: 13px; background-color: #ffffff;">
                            @error('topic') <span style="font-size: 11px; color: #ef4444;">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Target Level</label>
                                <select wire:model.live="target_level" style="width: 100%; border-radius: 10px; border: 1px solid #cbd5e1; padding: 8px 10px; font-size: 12px; background-color: #ffffff;">
                                    <option value="national">National</option>
                                    <option value="region">Regional</option>
                                    <option value="county">County Level</option>
                                    <option value="constituency">Constituency Level</option>
                                </select>
                            </div>

                            <div>
                                <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Poll Status</label>
                                <select wire:model="status" style="width: 100%; border-radius: 10px; border: 1px solid #cbd5e1; padding: 8px 10px; font-size: 12px; background-color: #ffffff;">
                                    <option value="live">🟢 Live</option>
                                    <option value="ended">🔴 Ended</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Position / Office Title *</label>
                            <input type="text" wire:model="position_title" placeholder="e.g. Woman Representative, Governor, Senator, President" style="width: 100%; border-radius: 10px; border: 1px solid #cbd5e1; padding: 9px 14px; font-size: 13px; background-color: #ffffff;">
                        </div>

                        <!-- Allow Public Voting Toggle -->
                        <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 12px; display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <span style="display: block; font-size: 12px; font-weight: 800; color: #0f172a;">Allow Visitor Voting</span>
                                <span style="font-size: 10px; color: #64748b;">Enable to let visitors vote live on site.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="allow_public_voting" class="sr-only peer">
                                <div class="w-9 h-5 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand-navy"></div>
                            </label>
                        </div>

                        @if($target_level !== 'national')
                            <div>
                                <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Region Target</label>
                                <select wire:model.live="region_id" style="width: 100%; border-radius: 10px; border: 1px solid #cbd5e1; padding: 8px 10px; font-size: 12px; background-color: #ffffff;">
                                    <option value="">-- Select Region --</option>
                                    @foreach($regions as $reg)
                                        <option value="{{ $reg->id }}">{{ $reg->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if(in_array($target_level, ['county', 'constituency']))
                            <div>
                                <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">County Target</label>
                                <select wire:model.live="county_id" style="width: 100%; border-radius: 10px; border: 1px solid #cbd5e1; padding: 8px 10px; font-size: 12px; background-color: #ffffff;">
                                    <option value="">-- Select County --</option>
                                    @foreach($counties as $co)
                                        <option value="{{ $co->id }}">{{ $co->name }} (Code {{ $co->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if($target_level === 'constituency' && $county_id)
                            <div>
                                <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Constituency Target</label>
                                <select wire:model="constituency_id" style="width: 100%; border-radius: 10px; border: 1px solid #cbd5e1; padding: 8px 10px; font-size: 12px; background-color: #ffffff;">
                                    <option value="">-- Select Constituency --</option>
                                    @foreach($constituencies as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- Politician Candidates Picker -->
                        <div class="space-y-2 pt-2 border-t border-gray-100" x-data="{ initVotes: 0 }">
                            <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Assign Politicians & Initial Vote Count</label>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <select id="politician_picker_create" style="flex: 1; border-radius: 10px; border: 1px solid #cbd5e1; padding: 8px 10px; font-size: 12px; background-color: #ffffff;" x-ref="polSelectCreate">
                                    <option value="">-- Pick Candidate --</option>
                                    @foreach($availablePoliticians as $pol)
                                        <option value="{{ $pol->id }}">{{ $pol->name }} ({{ $pol->politicalParty->abbreviation ?? 'IND' }})</option>
                                    @endforeach
                                </select>
                                <input type="number" x-model="initVotes" placeholder="Votes" style="width: 80px; border-radius: 10px; border: 1px solid #cbd5e1; padding: 8px 10px; font-size: 12px; font-family: monospace; text-align: right; background-color: #ffffff;">
                                <button type="button" @click="$wire.addPoliticianCandidate($refs.polSelectCreate.value, initVotes); $refs.polSelectCreate.value = ''; initVotes = 0;" style="background-color: #0f172a; color: #ffffff; font-weight: 700; font-size: 12px; padding: 8px 14px; border-radius: 10px; border: none; cursor: pointer;">
                                    + Add
                                </button>
                            </div>

                            <!-- Selected Candidates List -->
                            @if(count($selectedPoliticians) > 0)
                                <div class="space-y-2 pt-2">
                                    @foreach($selectedPoliticians as $idx => $item)
                                        @php $candidateObj = \App\Modules\PublicOpinion\Models\Politician::find($item['politician_id']); @endphp
                                        @if($candidateObj)
                                            <div style="display: flex; align-items: center; justify-content: space-between; background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 8px 12px; border-radius: 10px; font-size: 12px;">
                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                    <img src="{{ asset($candidateObj->photo_path ?: '/images/favicon.png') }}" onerror="this.onerror=null; this.src='/images/favicon.png';" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover;">
                                                    <div>
                                                        <span style="font-weight: 800; color: #0f172a; display: block;">{{ $candidateObj->name }}</span>
                                                        <span style="font-size: 10px; font-weight: 700; color: #64748b;">{{ $candidateObj->politicalParty->abbreviation ?? 'IND' }}</span>
                                                    </div>
                                                </div>
                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                    <input type="number" wire:model="selectedPoliticians.{{ $idx }}.votes" style="width: 80px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 4px 8px; font-size: 12px; font-family: monospace; text-align: right; background-color: #ffffff;">
                                                    <span style="font-size: 10px; color: #94a3b8;">votes</span>
                                                    <button type="button" wire:click="removePoliticianCandidate({{ $idx }})" style="color: #ef4444; font-weight: 800; background: none; border: none; cursor: pointer; padding: 0 4px;">✕</button>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Custom Options Inputs with Photo URL & Gallery picker -->
                        <div class="space-y-2 pt-2 border-t border-gray-100">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Custom Candidates, Photos & Votes</label>
                                <button type="button" wire:click="addCustomOption" style="font-size: 12px; color: #2563eb; font-weight: 700; background: none; border: none; cursor: pointer;">+ Add Row</button>
                            </div>

                            @foreach($customOptions as $index => $cOpt)
                                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 8px; border-radius: 10px;">
                                    <input type="text" wire:model="customOptions.{{ $index }}.name" placeholder="Candidate Name..." style="flex: 1; min-width: 140px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 6px 10px; font-size: 12px; background-color: #ffffff;">
                                    <input type="text" wire:model="customOptions.{{ $index }}.photo" placeholder="Photo URL..." style="flex: 1; min-width: 140px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 6px 10px; font-size: 12px; background-color: #ffffff;">
                                    <button type="button" wire:click="openMediaGallery('custom', null, {{ $index }})" style="background-color: #2563eb; color: #ffffff; font-weight: 700; font-size: 10px; padding: 6px 10px; border-radius: 6px; border: none; cursor: pointer;">
                                        🖼️ Gallery
                                    </button>
                                    <input type="number" wire:model="customOptions.{{ $index }}.votes" placeholder="Votes" style="width: 75px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 6px 8px; font-size: 12px; font-family: monospace; text-align: right; background-color: #ffffff;">
                                    <button type="button" wire:click="removeCustomOption({{ $index }})" style="color: #ef4444; font-weight: 800; background: none; border: none; cursor: pointer; padding: 0 4px;">✕</button>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: flex-end; gap: 12px; background-color: #f8fafc; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
                        <button type="button" wire:click="closeCreateModal" style="background-color: #ffffff; border: 1px solid #cbd5e1; color: #475569; font-weight: 700; font-size: 13px; padding: 8px 16px; border-radius: 10px; cursor: pointer;">
                            Cancel
                        </button>
                        <button type="submit" style="background-color: #0f172a; color: #ffffff; font-weight: 700; font-size: 13px; padding: 8px 20px; border-radius: 10px; border: none; cursor: pointer;">
                            Publish Poll Results
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif

    <!-- Edit Poll Pop-Up Modal Window -->
    @if($showEditModal)
        <div style="position: fixed; inset: 0; background-color: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 16px; overflow-y: auto;">
            <div style="background-color: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; width: 100%; max-width: 680px; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);" class="font-sans">
                
                <!-- Modal Header -->
                <div style="padding: 18px 24px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; background-color: #f8fafc; border-top-left-radius: 20px; border-top-right-radius: 20px;">
                    <div>
                        <h3 style="font-size: 18px; font-weight: 800; color: #0f172a;">✏️ Edit Poll #{{ $editingPollId }}</h3>
                        <p style="font-size: 12px; color: #64748b; margin-top: 2px;">Update topic, candidates, candidate photos, target level, or vote tallies</p>
                    </div>
                    <button wire:click="closeEditModal" style="background-color: #e2e8f0; border: none; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #475569; font-weight: 700; cursor: pointer;">
                        ✕
                    </button>
                </div>

                <!-- Modal Form Body -->
                <div style="padding: 24px; overflow-y: auto; flex: 1;" class="space-y-4">
                    <div>
                        <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Poll Topic / Title *</label>
                        <input type="text" wire:model="topic" style="width: 100%; border-radius: 10px; border: 1px solid #cbd5e1; padding: 9px 14px; font-size: 13px; background-color: #ffffff;">
                        @error('topic') <span style="font-size: 11px; color: #ef4444;">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Target Level</label>
                            <select wire:model.live="target_level" style="width: 100%; border-radius: 10px; border: 1px solid #cbd5e1; padding: 8px 10px; font-size: 12px; background-color: #ffffff;">
                                <option value="national">National</option>
                                <option value="region">Regional</option>
                                <option value="county">County Level</option>
                                <option value="constituency">Constituency Level</option>
                            </select>
                        </div>

                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Poll Status</label>
                            <select wire:model="status" style="width: 100%; border-radius: 10px; border: 1px solid #cbd5e1; padding: 8px 10px; font-size: 12px; background-color: #ffffff;">
                                <option value="live">🟢 Live</option>
                                <option value="ended">🔴 Ended</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Position / Office Title *</label>
                        <input type="text" wire:model="position_title" style="width: 100%; border-radius: 10px; border: 1px solid #cbd5e1; padding: 9px 14px; font-size: 13px; background-color: #ffffff;">
                    </div>

                    <!-- Allow Public Voting Toggle -->
                    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 12px; display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <span style="display: block; font-size: 12px; font-weight: 800; color: #0f172a;">Allow Visitor Voting</span>
                            <span style="font-size: 10px; color: #64748b;">Enable to let visitors vote live on site.</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="allow_public_voting" class="sr-only peer">
                            <div class="w-9 h-5 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand-navy"></div>
                        </label>
                    </div>

                    @if($target_level !== 'national')
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Region Target</label>
                            <select wire:model.live="region_id" style="width: 100%; border-radius: 10px; border: 1px solid #cbd5e1; padding: 8px 10px; font-size: 12px; background-color: #ffffff;">
                                <option value="">-- Select Region --</option>
                                @foreach($regions as $reg)
                                    <option value="{{ $reg->id }}">{{ $reg->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if(in_array($target_level, ['county', 'constituency']))
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">County Target</label>
                            <select wire:model.live="county_id" style="width: 100%; border-radius: 10px; border: 1px solid #cbd5e1; padding: 8px 10px; font-size: 12px; background-color: #ffffff;">
                                <option value="">-- Select County --</option>
                                @foreach($counties as $co)
                                    <option value="{{ $co->id }}">{{ $co->name }} (Code {{ $co->code }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if($target_level === 'constituency' && $county_id)
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Constituency Target</label>
                            <select wire:model="constituency_id" style="width: 100%; border-radius: 10px; border: 1px solid #cbd5e1; padding: 8px 10px; font-size: 12px; background-color: #ffffff;">
                                <option value="">-- Select Constituency --</option>
                                @foreach($constituencies as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Politician Candidates Picker -->
                    <div class="space-y-2 pt-2 border-t border-gray-100" x-data="{ initVotes: 0 }">
                        <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Assign Politicians & Votes</label>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <select id="politician_picker_modal" style="flex: 1; border-radius: 10px; border: 1px solid #cbd5e1; padding: 8px 10px; font-size: 12px; background-color: #ffffff;" x-ref="polSelectModal">
                                <option value="">-- Pick Candidate --</option>
                                @foreach($availablePoliticians as $pol)
                                    <option value="{{ $pol->id }}">{{ $pol->name }} ({{ $pol->politicalParty->abbreviation ?? 'IND' }})</option>
                                @endforeach
                            </select>
                            <input type="number" x-model="initVotes" placeholder="Votes" style="width: 80px; border-radius: 10px; border: 1px solid #cbd5e1; padding: 8px 10px; font-size: 12px; font-family: monospace; text-align: right; background-color: #ffffff;">
                            <button type="button" @click="$wire.addPoliticianCandidate($refs.polSelectModal.value, initVotes); $refs.polSelectModal.value = ''; initVotes = 0;" style="background-color: #0f172a; color: #ffffff; font-weight: 700; font-size: 12px; padding: 8px 14px; border-radius: 10px; border: none; cursor: pointer;">
                                + Add
                            </button>
                        </div>

                        <!-- Selected Candidates List -->
                        @if(count($selectedPoliticians) > 0)
                            <div class="space-y-2 pt-2">
                                @foreach($selectedPoliticians as $idx => $item)
                                    @php $candidateObj = \App\Modules\PublicOpinion\Models\Politician::find($item['politician_id']); @endphp
                                    @if($candidateObj)
                                        <div style="display: flex; align-items: center; justify-content: space-between; background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 8px 12px; border-radius: 10px; font-size: 12px;">
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <img src="{{ asset($candidateObj->photo_path ?: '/images/favicon.png') }}" onerror="this.onerror=null; this.src='/images/favicon.png';" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover;">
                                                <div>
                                                    <span style="font-weight: 800; color: #0f172a; display: block;">{{ $candidateObj->name }}</span>
                                                    <span style="font-size: 10px; font-weight: 700; color: #64748b;">{{ $candidateObj->politicalParty->abbreviation ?? 'IND' }}</span>
                                                </div>
                                            </div>
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <input type="number" wire:model="selectedPoliticians.{{ $idx }}.votes" style="width: 80px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 4px 8px; font-size: 12px; font-family: monospace; text-align: right; background-color: #ffffff;">
                                                <span style="font-size: 10px; color: #94a3b8;">votes</span>
                                                <button type="button" wire:click="removePoliticianCandidate({{ $idx }})" style="color: #ef4444; font-weight: 800; background: none; border: none; cursor: pointer; padding: 0 4px;">✕</button>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Custom Options Inputs with Photo URL & Gallery picker -->
                    <div class="space-y-2 pt-2 border-t border-gray-100">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Custom Candidates, Photos & Votes</label>
                            <button type="button" wire:click="addCustomOption" style="font-size: 12px; color: #2563eb; font-weight: 700; background: none; border: none; cursor: pointer;">+ Add Row</button>
                        </div>

                        @foreach($customOptions as $index => $cOpt)
                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 8px; border-radius: 10px;">
                                <input type="text" wire:model="customOptions.{{ $index }}.name" placeholder="Candidate Name..." style="flex: 1; min-width: 140px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 6px 10px; font-size: 12px; background-color: #ffffff;">
                                <input type="text" wire:model="customOptions.{{ $index }}.photo" placeholder="Photo URL..." style="flex: 1; min-width: 140px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 6px 10px; font-size: 12px; background-color: #ffffff;">
                                <button type="button" wire:click="openMediaGallery('custom', null, {{ $index }})" style="background-color: #2563eb; color: #ffffff; font-weight: 700; font-size: 10px; padding: 6px 10px; border-radius: 6px; border: none; cursor: pointer;">
                                    🖼️ Gallery
                                </button>
                                <input type="number" wire:model="customOptions.{{ $index }}.votes" placeholder="Votes" style="width: 75px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 6px 8px; font-size: 12px; font-family: monospace; text-align: right; background-color: #ffffff;">
                                <button type="button" wire:click="removeCustomOption({{ $index }})" style="color: #ef4444; font-weight: 800; background: none; border: none; cursor: pointer; padding: 0 4px;">✕</button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Modal Footer -->
                <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: flex-end; gap: 12px; background-color: #f8fafc; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
                    <button type="button" wire:click="closeEditModal" style="background-color: #ffffff; border: 1px solid #cbd5e1; color: #475569; font-weight: 700; font-size: 13px; padding: 8px 16px; border-radius: 10px; cursor: pointer;">
                        Cancel
                    </button>
                    <button type="button" wire:click="createPoll" style="background-color: #0f172a; color: #ffffff; font-weight: 700; font-size: 13px; padding: 8px 20px; border-radius: 10px; border: none; cursor: pointer;">
                        Save Poll Changes
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>
