<?php

namespace App\Modules\PublicOpinion\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Modules\PublicOpinion\Models\MediaAsset;
use App\Modules\PublicOpinion\Models\Politician;
use Livewire\Attributes\Title;

#[Title('Media Gallery & Asset Manager - Metrica Polls')]
class AdminMediaGallery extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $uploadedImages = [];
    public $category = 'candidate_photo';
    public $searchQuery = '';
    public $filterCategory = 'all';
    public $perPage = 18;

    public $selectedAsset = null;
    public $showPreviewModal = false;
    public $editingAssetId = null;
    public $editingName = '';

    protected $rules = [
        'uploadedImages.*' => 'image|max:10240', // 10MB max per image
    ];

    public function updatingSearchQuery()
    {
        $this->resetPage();
    }

    public function updatingFilterCategory()
    {
        $this->resetPage();
    }

    public function uploadAssets()
    {
        $this->validate([
            'uploadedImages' => 'required|array|min:1',
            'uploadedImages.*' => 'image|max:10240',
        ]);

        $count = 0;
        foreach ($this->uploadedImages as $file) {
            $path = $file->store('candidate_media', 'public');
            $publicUrl = '/storage/' . $path;

            MediaAsset::create([
                'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'file_path' => $publicUrl,
                'category' => $this->category,
                'file_size' => $file->getSize(),
            ]);
            $count++;
        }

        $this->uploadedImages = [];
        session()->flash('success', "Successfully uploaded {$count} image(s) to Media Gallery!");
    }

    public function openPreview($id)
    {
        $this->selectedAsset = MediaAsset::find($id);
        $this->showPreviewModal = true;
    }

    public function closePreview()
    {
        $this->showPreviewModal = false;
        $this->selectedAsset = null;
    }

    public function startEditName($id)
    {
        $asset = MediaAsset::find($id);
        if ($asset) {
            $this->editingAssetId = $asset->id;
            $this->editingName = $asset->name;
        }
    }

    public function saveName($id)
    {
        $asset = MediaAsset::find($id);
        if ($asset && trim($this->editingName) !== '') {
            $asset->update(['name' => trim($this->editingName)]);
            session()->flash('success', "Updated title for asset #{$id}.");
        }
        $this->editingAssetId = null;
        $this->editingName = '';
    }

    public function deleteAsset($id)
    {
        $asset = MediaAsset::find($id);
        if ($asset) {
            $asset->delete();
            if ($this->selectedAsset && $this->selectedAsset->id == $id) {
                $this->closePreview();
            }
            session()->flash('success', 'Media asset deleted from gallery.');
        }
    }

    public function render()
    {
        // Seed initial politician photos into MediaAsset table if empty
        if (MediaAsset::count() === 0) {
            $politicians = Politician::whereNotNull('photo_path')->get();
            foreach ($politicians as $pol) {
                MediaAsset::create([
                    'name' => $pol->name . ' Headshot',
                    'file_path' => $pol->photo_path,
                    'category' => 'candidate_photo',
                ]);
            }
        }

        $query = MediaAsset::latest();

        if ($this->filterCategory !== 'all') {
            $query->where('category', $this->filterCategory);
        }

        if (trim($this->searchQuery) !== '') {
            $q = trim($this->searchQuery);
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('file_path', 'like', "%{$q}%");
            });
        }

        $assets = $query->paginate((int) $this->perPage);

        return view('PublicOpinion::livewire.admin-media-gallery', [
            'assets' => $assets,
        ])->layout('Dashboard::admin-layout');
    }
}
