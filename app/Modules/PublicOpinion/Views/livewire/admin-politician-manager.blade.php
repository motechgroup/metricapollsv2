@section('page_title', 'Politicians & Candidates Manager')

<div class="space-y-6 font-sans w-full">
    <!-- Top Header Bar with Prominent Register New Politician Button -->
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 20px;" class="shadow-sm">
        <div>
            <h1 style="font-size: 24px; font-weight: 800; color: #0f172a;">Politician & Candidate Profiles Manager</h1>
            <p style="font-size: 13px; color: #64748b; margin-top: 2px;">Register candidates, upload headshots, select images from gallery, link political parties, and assign geographic scopes.</p>
        </div>

        <div>
            <button wire:click="openRegisterModal" style="display: inline-flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 800; color: #ffffff; background-color: #0f172a; padding: 12px 20px; border-radius: 12px; border: none; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); transition: all 150ms ease;">
                <span style="font-size: 16px;">➕</span>
                <span>Register New Politician</span>
            </button>
        </div>
    </div>

    @if(session()->has('success'))
        <div style="padding: 14px 18px; background-color: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; font-size: 14px; font-weight: 600; border-radius: 14px;">
            ✅ {{ session('success') }}
        </div>
    @endif

    <!-- Managed Politicians Full-Width Table Panel -->
    <div class="space-y-4 w-full">
        <!-- Filter, Categories & Search Bar -->
        <div style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 16px;" class="shadow-sm space-y-3">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <!-- Level Category Filters -->
                <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                    <span style="font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase;">Level:</span>
                    <button wire:click="$set('filterLevel', 'all')" style="{{ $filterLevel === 'all' ? 'background-color: #0f172a; color: #ffffff;' : 'background-color: #f1f5f9; color: #475569;' }} border: none; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;">
                        All ({{ \App\Modules\PublicOpinion\Models\Politician::count() }})
                    </button>
                    <button wire:click="$set('filterLevel', 'national')" style="{{ $filterLevel === 'national' ? 'background-color: #2563eb; color: #ffffff;' : 'background-color: #eff6ff; color: #1d4ed8;' }} border: 1px solid #bfdbfe; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;">
                        National
                    </button>
                    <button wire:click="$set('filterLevel', 'region')" style="{{ $filterLevel === 'region' ? 'background-color: #7c3aed; color: #ffffff;' : 'background-color: #f5f3ff; color: #6d28d9;' }} border: 1px solid #ddd6fe; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;">
                        Regional
                    </button>
                    <button wire:click="$set('filterLevel', 'county')" style="{{ $filterLevel === 'county' ? 'background-color: #059669; color: #ffffff;' : 'background-color: #ecfdf5; color: #047857;' }} border: 1px solid #a7f3d0; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;">
                        County
                    </button>
                    <button wire:click="$set('filterLevel', 'constituency')" style="{{ $filterLevel === 'constituency' ? 'background-color: #d97706; color: #ffffff;' : 'background-color: #fffbeb; color: #b45309;' }} border: 1px solid #fde68a; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;">
                        Constituency
                    </button>
                </div>

                <!-- Party & County Dropdown Filters -->
                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                    <select wire:model.live="filterParty" style="border-radius: 10px; border: 1px solid #cbd5e1; padding: 7px 12px; font-size: 12px; background-color: #ffffff;">
                        <option value="">All Political Parties</option>
                        @foreach($parties as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->abbreviation }})</option>
                        @endforeach
                    </select>

                    <select wire:model.live="filterCounty" style="border-radius: 10px; border: 1px solid #cbd5e1; padding: 7px 12px; font-size: 12px; background-color: #ffffff;">
                        <option value="">All Counties</option>
                        @foreach($counties as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Search Input & Per Page Controls -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2 border-t border-gray-100">
                <div class="w-full sm:flex-1">
                    <input type="text" wire:model.live.debounce.250ms="search" placeholder="🔍 Search politician by candidate name or position title..." style="width: 100%; border-radius: 10px; border: 1px solid #cbd5e1; padding: 8px 14px; font-size: 13px; background-color: #ffffff;">
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

        <!-- Clean Data Table List Format -->
        <div style="background-color: #ffffff; border: 1px solid #cbd5e1; border-radius: 20px; overflow: hidden;" class="shadow-xs w-full">
            <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
                <thead>
                    <tr style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">
                        <th style="padding: 14px 20px; width: 60px;">Photo</th>
                        <th style="padding: 14px 20px;">Candidate Name</th>
                        <th style="padding: 14px 20px; width: 140px;">Political Party</th>
                        <th style="padding: 14px 20px; width: 180px;">Office / Position Title</th>
                        <th style="padding: 14px 20px;">Geographic Scope Target</th>
                        <th style="padding: 14px 20px; width: 140px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody style="divide-y: 1px solid #f1f5f9;">
                    @forelse($politicians as $politician)
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: background-color 150ms ease;" class="hover:bg-slate-50/80">
                            <!-- Avatar Column -->
                            <td style="padding: 14px 20px; vertical-align: middle;">
                                <img src="{{ asset($politician->photo_path ?: '/images/favicon.png') }}" onerror="this.onerror=null; this.src='/images/favicon.png';" style="width: 42px; height: 42px; border-radius: 50%; object-fit: cover; border: 2px solid #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                            </td>

                            <!-- Candidate Name -->
                            <td style="padding: 14px 20px; vertical-align: middle;">
                                <div style="font-weight: 800; color: #0f172a; font-size: 15px; line-height: 1.3;">{{ $politician->name }}</div>
                                @if($politician->bio)
                                    <div style="font-size: 11px; color: #64748b; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 280px;" title="{{ $politician->bio }}">
                                        {{ $politician->bio }}
                                    </div>
                                @endif
                            </td>

                            <!-- Political Party -->
                            <td style="padding: 14px 20px; vertical-align: middle;">
                                @if($politician->politicalParty)
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 8px; font-size: 11px; font-weight: 800; color: #1e293b; background-color: #f1f5f9; border: 1px solid #cbd5e1;">
                                        <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background-color: {{ $politician->politicalParty->party_color ?? '#2563eb' }};"></span>
                                        {{ $politician->politicalParty->abbreviation }}
                                    </span>
                                @else
                                    <span style="font-size: 11px; font-weight: 700; color: #64748b; background-color: #f1f5f9; padding: 2px 8px; border-radius: 6px;">IND</span>
                                @endif
                            </td>

                            <!-- Position Title -->
                            <td style="padding: 14px 20px; vertical-align: middle; font-weight: 700; color: #334155; font-size: 13px;">
                                {{ $politician->position_title ?: 'General Office' }}
                            </td>

                            <!-- Location Scope -->
                            <td style="padding: 14px 20px; vertical-align: middle;">
                                <span style="font-size: 11px; font-weight: 700; color: #2563eb; background-color: #eff6ff; padding: 3px 8px; border-radius: 6px; border: 1px solid #bfdbfe;">
                                    📍 
                                    @if($politician->constituency)
                                        {{ $politician->constituency->name }} Constituency ({{ $politician->county->name ?? '' }})
                                    @elseif($politician->county)
                                        {{ $politician->county->name }} County
                                    @elseif($politician->region)
                                        {{ $politician->region->name }} Region
                                    @else
                                        National Target
                                    @endif
                                </span>
                            </td>

                            <!-- Actions Column -->
                            <td style="padding: 14px 20px; vertical-align: middle; text-align: right;">
                                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 6px;">
                                    <button wire:click="openEditModal({{ $politician->id }})" style="font-size: 11px; font-weight: 700; color: #92400e; background-color: #fef3c7; padding: 5px 10px; border-radius: 6px; border: 1px solid #fde68a; cursor: pointer;">
                                        ✏️ Edit
                                    </button>

                                    <button wire:click="deletePolitician({{ $politician->id }})" wire:confirm="Are you sure you want to delete this politician profile?" style="font-size: 11px; font-weight: 700; color: #991b1b; background-color: #fef2f2; padding: 5px 10px; border-radius: 6px; border: 1px solid #fecaca; cursor: pointer;">
                                        🗑️ Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 40px; text-align: center; color: #64748b; font-size: 14px;">
                                No politicians found matching the specified filters or search query.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Livewire Pagination Links Bar -->
            @if($politicians->hasPages())
                <div style="padding: 16px 20px; background-color: #f8fafc; border-top: 1px solid #e2e8f0;">
                    {{ $politicians->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Register / Edit Politician Pop-Up Modal Window -->
    @if($showModal)
        <div style="position: fixed; inset: 0; background-color: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 16px; overflow-y: auto;">
            <div style="background-color: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; width: 100%; max-width: 640px; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);" class="font-sans">
                
                <!-- Modal Header -->
                <div style="padding: 18px 24px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; background-color: #f8fafc; border-top-left-radius: 20px; border-top-right-radius: 20px;">
                    <div>
                        <h3 style="font-size: 18px; font-weight: 800; color: #0f172a;">
                            {{ $editingPoliticianId ? '✏️ Edit Politician Profile #' . $editingPoliticianId : '➕ Register New Politician / Candidate' }}
                        </h3>
                        <p style="font-size: 12px; color: #64748b; margin-top: 2px;">Enter full name, upload headshot, pick image from gallery, link party, and assign target level</p>
                    </div>
                    <button wire:click="closeModal" style="background-color: #e2e8f0; border: none; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #475569; font-weight: 700; cursor: pointer;">
                        ✕
                    </button>
                </div>

                <!-- Modal Form Body -->
                <form wire:submit.prevent="savePolitician" style="display: flex; flex-direction: column; flex: 1; overflow: hidden;">
                    <div style="padding: 24px; overflow-y: auto; flex: 1;" class="space-y-4">
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Full Name *</label>
                            <input type="text" wire:model="name" placeholder="e.g. Hon. Dorice Donya Aburi" style="width: 100%; border-radius: 10px; border: 1px solid #cbd5e1; padding: 9px 14px; font-size: 13px; background-color: #ffffff;">
                            @error('name') <span style="font-size: 11px; color: #ef4444;">{{ $message }}</span> @enderror
                        </div>

                        <!-- Candidate Headshot Image Upload, Photo URL, or Gallery Picker -->
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Candidate Headshot Picture</label>
                            
                            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px;" class="space-y-2">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <input type="file" wire:model="photo" accept="image/*" style="font-size: 12px; color: #475569; flex: 1;">
                                    
                                    <button type="button" wire:click="openMediaGallery" style="background-color: #2563eb; color: #ffffff; font-weight: 700; font-size: 11px; padding: 8px 14px; border-radius: 8px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                        🖼️ Select from Gallery
                                    </button>
                                </div>

                                <div style="display: flex; align-items: center; gap: 8px; pt-1;">
                                    <span style="font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase;">Image Path / URL:</span>
                                    <input type="text" wire:model="photo_url" placeholder="/images/politicians/headshot.jpg or https://..." style="flex: 1; border-radius: 8px; border: 1px solid #cbd5e1; padding: 6px 10px; font-size: 12px; background-color: #ffffff;">
                                </div>
                            </div>
                            @error('photo') <span style="font-size: 11px; color: #ef4444;">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Political Party</label>
                                <select wire:model="political_party_id" style="width: 100%; border-radius: 10px; border: 1px solid #cbd5e1; padding: 8px 10px; font-size: 12px; background-color: #ffffff;">
                                    <option value="">-- Independent / None --</option>
                                    @foreach($parties as $party)
                                        <option value="{{ $party->id }}">{{ $party->name }} ({{ $party->abbreviation }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Target Position Title *</label>
                                <input type="text" wire:model="position_title" placeholder="e.g. Governor, Woman Representative" style="width: 100%; border-radius: 10px; border: 1px solid #cbd5e1; padding: 9px 14px; font-size: 13px; background-color: #ffffff;">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Geographic Level</label>
                                <select wire:model.live="level" style="width: 100%; border-radius: 10px; border: 1px solid #cbd5e1; padding: 8px 10px; font-size: 12px; background-color: #ffffff;">
                                    <option value="national">National Level</option>
                                    <option value="region">Regional Level</option>
                                    <option value="county">County Level</option>
                                    <option value="constituency">Constituency Level</option>
                                </select>
                            </div>

                            @if($level !== 'national')
                                <div>
                                    <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Region Target</label>
                                    <select wire:model.live="region_id" style="width: 100%; border-radius: 10px; border: 1px solid #cbd5e1; padding: 8px 10px; font-size: 12px; background-color: #ffffff;">
                                        <option value="">-- Select Region --</option>
                                        @foreach($regions as $r)
                                            <option value="{{ $r->id }}">{{ $r->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>

                        @if(in_array($level, ['county', 'constituency']))
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

                        @if($level === 'constituency' && $county_id)
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

                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Bio / Summary</label>
                            <textarea wire:model="bio" rows="3" placeholder="Brief background summary of politician..." style="width: 100%; border-radius: 10px; border: 1px solid #cbd5e1; padding: 9px 14px; font-size: 12px; background-color: #ffffff;"></textarea>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: flex-end; gap: 12px; background-color: #f8fafc; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
                        <button type="button" wire:click="closeModal" style="background-color: #ffffff; border: 1px solid #cbd5e1; color: #475569; font-weight: 700; font-size: 13px; padding: 8px 16px; border-radius: 10px; cursor: pointer;">
                            Cancel
                        </button>
                        <button type="submit" style="background-color: #0f172a; color: #ffffff; font-weight: 700; font-size: 13px; padding: 8px 20px; border-radius: 10px; border: none; cursor: pointer;">
                            {{ $editingPoliticianId ? 'Save Profile Changes' : 'Register Politician' }}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif

    <!-- Media Library & Gallery Picker Modal Overlay -->
    @if($showMediaGalleryModal)
        <div style="position: fixed; inset: 0; background-color: rgba(15, 23, 42, 0.75); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 10000; padding: 16px; overflow-y: auto;">
            <div style="background-color: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; width: 100%; max-width: 800px; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);" class="font-sans">
                
                <!-- Modal Header -->
                <div style="padding: 18px 24px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; background-color: #f8fafc; border-top-left-radius: 20px; border-top-right-radius: 20px;">
                    <div>
                        <h3 style="font-size: 18px; font-weight: 800; color: #0f172a;">🖼️ Select Candidate Headshot from Media Gallery</h3>
                        <p style="font-size: 12px; color: #64748b; margin-top: 2px;">Upload a local headshot image or click any saved candidate photo from the gallery.</p>
                    </div>
                    <button wire:click="closeMediaGallery" style="background-color: #e2e8f0; border: none; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #475569; font-weight: 700; cursor: pointer;">
                        ✕
                    </button>
                </div>

                <!-- Modal Body -->
                <div style="padding: 24px; overflow-y: auto; flex: 1;" class="space-y-6">
                    <!-- Local Upload Box inside Gallery -->
                    <div style="background-color: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 16px; padding: 20px; text-align: center;">
                        <form wire:submit.prevent="uploadNewMediaAsset" class="space-y-3">
                            <div style="font-size: 13px; font-weight: 800; color: #0f172a;">📁 Upload New Headshot from Device</div>
                            <div style="font-size: 11px; color: #64748b;">Select image file from computer (JPG, PNG, WEBP - Max 5MB)</div>

                            <div style="display: flex; align-items: center; justify-content: center; gap: 12px;">
                                <input type="file" wire:model="uploadedGalleryImage" accept="image/*" style="font-size: 12px; color: #475569;">
                                @if($uploadedGalleryImage)
                                    <button type="submit" style="background-color: #0f172a; color: #ffffff; font-weight: 700; font-size: 12px; padding: 8px 16px; border-radius: 10px; border: none; cursor: pointer;">
                                        ⬆️ Upload & Select Image
                                    </button>
                                @endif
                            </div>

                            @error('uploadedGalleryImage') <span style="display: block; font-size: 11px; color: #ef4444;">{{ $message }}</span> @enderror

                            <div wire:loading wire:target="uploadedGalleryImage" style="font-size: 11px; color: #2563eb; font-weight: 700;">
                                ⏳ Uploading headshot...
                            </div>
                        </form>
                    </div>

                    <!-- Gallery Search -->
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                        <h4 style="font-size: 14px; font-weight: 800; color: #0f172a;">Select Saved Headshot ({{ count($mediaAssets) }} Images)</h4>
                        <input type="text" wire:model.live.debounce.250ms="mediaSearch" placeholder="🔍 Search gallery photos..." style="width: 260px; border-radius: 10px; border: 1px solid #cbd5e1; padding: 7px 12px; font-size: 12px; background-color: #ffffff;">
                    </div>

                    <!-- Image Thumbnail Grid -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 12px; max-height: 320px; overflow-y: auto; padding: 4px;">
                        @forelse($mediaAssets as $asset)
                            <div wire:click="selectMediaAsset('{{ $asset->file_path }}')" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 8px; text-align: center; cursor: pointer; transition: all 150ms ease;" class="hover:border-blue-500 hover:shadow-md group">
                                <div style="width: 100%; height: 90px; border-radius: 8px; overflow: hidden; background-color: #f1f5f9; display: flex; align-items: center; justify-content: center; margin-bottom: 6px;">
                                    <img src="{{ asset($asset->file_path ?: '/images/favicon.png') }}" onerror="this.onerror=null; this.src='/images/favicon.png';" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <div style="font-size: 11px; font-weight: 800; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $asset->name }}">
                                    {{ $asset->name }}
                                </div>
                                <div style="font-size: 9px; font-weight: 700; color: #2563eb; margin-top: 2px;">
                                    Select Headshot ↗
                                </div>
                            </div>
                        @empty
                            <div style="grid-column: 1 / -1; padding: 24px; text-align: center; color: #64748b; font-size: 13px;">
                                No images found in gallery. Use the local file upload box above!
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Modal Footer -->
                <div style="padding: 14px 24px; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: flex-end; background-color: #f8fafc; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
                    <button type="button" wire:click="closeMediaGallery" style="background-color: #ffffff; border: 1px solid #cbd5e1; color: #475569; font-weight: 700; font-size: 13px; padding: 8px 16px; border-radius: 10px; cursor: pointer;">
                        Cancel
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>
