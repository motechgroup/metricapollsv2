@section('page_title', 'Media Gallery')

<div class="space-y-6 font-sans w-full">
    <!-- Header Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 20px;" class="shadow-sm">
        <div>
            <h1 style="font-size: 24px; font-weight: 800; color: #0f172a;">🖼️ System Media Gallery & Asset Manager</h1>
            <p style="font-size: 13px; color: #64748b; margin-top: 2px;">Upload, search, and manage candidate headshots, logos, and media for public opinion polls.</p>
        </div>

        <div style="font-size: 12px; font-weight: 800; color: #2563eb; background-color: #eff6ff; padding: 8px 16px; border-radius: 12px; border: 1px solid #bfdbfe;">
            Total Stored Assets: {{ \App\Modules\PublicOpinion\Models\MediaAsset::count() }}
        </div>
    </div>

    @if(session()->has('success'))
        <div style="padding: 14px 18px; background-color: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; font-size: 14px; font-weight: 600; border-radius: 14px;">
            ✅ {{ session('success') }}
        </div>
    @endif

    <!-- Drag & Drop Upload Section -->
    <div style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 24px;" class="shadow-sm">
        <h2 style="font-size: 16px; font-weight: 800; color: #0f172a; margin-bottom: 12px;">📁 Upload Local Images & Media Assets</h2>

        <form wire:submit.prevent="uploadAssets" class="space-y-4">
            <div style="background-color: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 16px; padding: 24px; text-align: center;">
                <div style="font-size: 14px; font-weight: 800; color: #0f172a; margin-bottom: 4px;">Select or Drag Files from Computer</div>
                <div style="font-size: 12px; color: #64748b; margin-bottom: 14px;">Upload single or multiple images (JPG, PNG, WEBP - Max 10MB per file)</div>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <input type="file" wire:model="uploadedImages" multiple accept="image/*" style="font-size: 12px; color: #475569;">

                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 11px; font-weight: 700; color: #475569;">Category:</span>
                        <select wire:model="category" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 6px 10px; font-size: 12px; background-color: #ffffff;">
                            <option value="candidate_photo">Candidate Headshots</option>
                            <option value="party_logo">Political Party Logos</option>
                            <option value="general">General Media</option>
                        </select>
                    </div>

                    @if(count($uploadedImages) > 0)
                        <button type="submit" style="background-color: #0f172a; color: #ffffff; font-weight: 700; font-size: 13px; padding: 8px 18px; border-radius: 10px; border: none; cursor: pointer;">
                            ⬆️ Upload {{ count($uploadedImages) }} File(s)
                        </button>
                    @endif
                </div>

                @error('uploadedImages.*') <span style="display: block; font-size: 11px; color: #ef4444; margin-top: 8px;">{{ $message }}</span> @enderror

                <div wire:loading wire:target="uploadedImages" style="font-size: 12px; color: #2563eb; font-weight: 700; margin-top: 10px;">
                    ⏳ Uploading media files to storage...
                </div>
            </div>
        </form>
    </div>

    <!-- Filter & Search Controls Bar -->
    <div style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 16px;" class="shadow-sm space-y-3">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <!-- Category Tabs -->
            <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                <span style="font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase;">Category:</span>
                <button wire:click="$set('filterCategory', 'all')" style="{{ $filterCategory === 'all' ? 'background-color: #0f172a; color: #ffffff;' : 'background-color: #f1f5f9; color: #475569;' }} border: none; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;">
                    All Assets
                </button>
                <button wire:click="$set('filterCategory', 'candidate_photo')" style="{{ $filterCategory === 'candidate_photo' ? 'background-color: #2563eb; color: #ffffff;' : 'background-color: #eff6ff; color: #1d4ed8;' }} border: 1px solid #bfdbfe; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;">
                    👤 Candidate Photos
                </button>
                <button wire:click="$set('filterCategory', 'party_logo')" style="{{ $filterCategory === 'party_logo' ? 'background-color: #7c3aed; color: #ffffff;' : 'background-color: #f5f3ff; color: #6d28d9;' }} border: 1px solid #ddd6fe; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;">
                    🚩 Party Logos
                </button>
                <button wire:click="$set('filterCategory', 'general')" style="{{ $filterCategory === 'general' ? 'background-color: #059669; color: #ffffff;' : 'background-color: #ecfdf5; color: #047857;' }} border: 1px solid #a7f3d0; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;">
                    🖼️ General
                </button>
            </div>

            <!-- Search Input -->
            <div class="w-full sm:w-auto flex items-center gap-3">
                <input type="text" wire:model.live.debounce.250ms="searchQuery" placeholder="🔍 Search gallery by filename or title..." style="width: 280px; border-radius: 10px; border: 1px solid #cbd5e1; padding: 7px 12px; font-size: 12px; background-color: #ffffff;">

                <select wire:model.live="perPage" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 6px 10px; font-size: 12px; background-color: #ffffff;">
                    <option value="12">12 per page</option>
                    <option value="18">18 per page</option>
                    <option value="36">36 per page</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Media Assets Visual Grid -->
    <div style="background-color: #ffffff; border: 1px solid #cbd5e1; border-radius: 20px; padding: 20px;" class="shadow-xs">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 16px;">
            @forelse($assets as $asset)
                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 12px; display: flex; flex-direction: column; justify-content: space-between; transition: all 150ms ease;" class="hover:border-blue-400 hover:shadow-md group">
                    <div>
                        <!-- Image Container -->
                        <div wire:click="openPreview({{ $asset->id }})" style="width: 100%; height: 140px; border-radius: 12px; overflow: hidden; background-color: #ffffff; border: 1px solid #e2e8f0; cursor: pointer; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; position: relative;">
                            <img src="{{ asset($asset->file_path ?: '/images/favicon.png') }}" onerror="this.onerror=null; this.src='/images/favicon.png';" style="width: 100%; height: 100%; object-fit: cover;">
                            
                            <div style="position: absolute; inset: 0; background-color: rgba(15, 23, 42, 0.4); opacity: 0; transition: opacity 150ms ease; display: flex; align-items: center; justify-content: center; color: #ffffff; font-weight: 800; font-size: 12px;" class="group-hover:opacity-100">
                                🔍 Preview
                            </div>
                        </div>

                        <!-- Editable Asset Name -->
                        @if($editingAssetId === $asset->id)
                            <div style="display: flex; align-items: center; gap: 4px; margin-bottom: 6px;">
                                <input type="text" wire:model="editingName" wire:keydown.enter="saveName({{ $asset->id }})" style="flex: 1; border-radius: 6px; border: 1px solid #cbd5e1; padding: 2px 6px; font-size: 11px; background-color: #ffffff;">
                                <button wire:click="saveName({{ $asset->id }})" style="background-color: #0f172a; color: #ffffff; font-size: 10px; padding: 2px 6px; border-radius: 6px; border: none; cursor: pointer;">✓</button>
                            </div>
                        @else
                            <div wire:click="startEditName({{ $asset->id }})" style="font-size: 12px; font-weight: 800; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; cursor: pointer;" title="Click to edit title: {{ $asset->name }}">
                                ✏️ {{ $asset->name }}
                            </div>
                        @endif

                        <div style="font-size: 10px; color: #64748b; margin-top: 2px; font-family: monospace;">
                            {{ strtoupper(str_replace('_', ' ', $asset->category)) }}
                        </div>
                    </div>

                    <!-- Bottom Action Buttons -->
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 10px; padding-top: 8px; border-top: 1px solid #e2e8f0;" x-data="{ copied: false }">
                        <button type="button" @click="navigator.clipboard.writeText('{{ asset($asset->file_path) }}'); copied = true; setTimeout(() => copied = false, 2000)" style="font-size: 10px; font-weight: 700; color: #2563eb; background: none; border: none; cursor: pointer;">
                            <span x-text="copied ? '✅ Copied' : '🔗 Copy Link'"></span>
                        </button>

                        <button wire:click="deleteAsset({{ $asset->id }})" wire:confirm="Are you sure you want to delete this image from Media Gallery?" style="font-size: 10px; font-weight: 700; color: #ef4444; background: none; border: none; cursor: pointer;">
                            🗑️ Delete
                        </button>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; padding: 40px; text-align: center; color: #64748b; font-size: 14px;">
                    No media assets found in gallery matching your criteria. Use the upload box above to add candidate headshots and party logos!
                </div>
            @endforelse
        </div>

        <!-- Pagination Links Bar -->
        @if($assets->hasPages())
            <div style="margin-top: 20px; padding-top: 16px; border-top: 1px solid #e2e8f0;">
                {{ $assets->links() }}
            </div>
        @endif
    </div>

    <!-- Preview Modal Window -->
    @if($showPreviewModal && $selectedAsset)
        <div style="position: fixed; inset: 0; background-color: rgba(15, 23, 42, 0.75); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 16px;">
            <div style="background-color: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; width: 100%; max-width: 540px; padding: 24px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);" class="font-sans space-y-4">
                
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <h3 style="font-size: 16px; font-weight: 800; color: #0f172a;">🖼️ {{ $selectedAsset->name }}</h3>
                    <button wire:click="closePreview" style="background-color: #e2e8f0; border: none; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #475569; font-weight: 700; cursor: pointer;">
                        ✕
                    </button>
                </div>

                <div style="max-height: 360px; overflow: hidden; border-radius: 14px; border: 1px solid #e2e8f0; background-color: #f8fafc; display: flex; align-items: center; justify-content: center;">
                    <img src="{{ asset($selectedAsset->file_path ?: '/images/favicon.png') }}" onerror="this.onerror=null; this.src='/images/favicon.png';" style="max-width: 100%; max-height: 360px; object-fit: contain;">
                </div>

                <div style="font-size: 12px; color: #64748b; background-color: #f8fafc; padding: 12px; border-radius: 10px; word-break: break-all;" class="space-y-1">
                    <div><strong>Asset Path:</strong> <code>{{ $selectedAsset->file_path }}</code></div>
                    <div><strong>Full URL:</strong> <code>{{ asset($selectedAsset->file_path) }}</code></div>
                </div>

                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
                    <button type="button" wire:click="deleteAsset({{ $selectedAsset->id }})" wire:confirm="Delete this asset permanently?" style="background-color: #fef2f2; color: #991b1b; border: 1px solid #fecaca; font-weight: 700; font-size: 12px; padding: 8px 14px; border-radius: 10px; cursor: pointer;">
                        Delete Asset
                    </button>
                    <button type="button" wire:click="closePreview" style="background-color: #0f172a; color: #ffffff; font-weight: 700; font-size: 12px; padding: 8px 16px; border-radius: 10px; border: none; cursor: pointer;">
                        Close
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>
