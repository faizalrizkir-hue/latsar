<?php

namespace App\Livewire;

use App\Models\DmsDocument;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class DmsTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public array $filters = [];
    public string $sort = 'updated';
    public string $dir = 'desc';
    public string $view = '';
    public int $perPage = 10;

    protected $queryString = [
        'sort' => ['except' => 'updated'],
        'dir' => ['except' => 'desc'],
        'view' => ['except' => '', 'history' => false],
        'perPage' => ['except' => 10],
    ];

    public function mount(array $filters = [], string $view = ''): void
    {
        $filters['doc_no'] = $filters['doc_no'] ?? '';
        $this->filters = $filters;
        $this->view = $view;
        $this->sort = $filters['sort'] ?? 'updated';
        $this->dir = strtolower($filters['dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
        $this->perPage = isset($filters['perPage']) ? (int)$filters['perPage'] : ($this->perPage);
        if (!in_array($this->perPage, [10,20,50,100,200,500], true)) {
            $this->perPage = 10;
        }
    }

    public function updatingFilters(): void
    {
        $this->resetPage();
    }

    public function updatingView(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    #[On('dms:set-filters')]
    public function setFilters(array $payload = []): void
    {
        foreach (['q','doc_no','status','type','tag','year_from','year_to'] as $key) {
            if (array_key_exists($key, $payload)) {
                $this->filters[$key] = $payload[$key];
            }
        }
        $this->resetPage();
    }

    public function sortBy(string $col): void
    {
        if ($this->sort === $col) {
            $this->dir = $this->dir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sort = $col;
            $this->dir = 'asc';
        }
        $this->resetPage();
    }

    #[On('dms:view-change')]
    public function setView(string $view = ''): void
    {
        $allowed = ['archive', 'trash', ''];
        $this->view = in_array($view, $allowed, true) ? $view : '';
        $this->resetPage();
        $this->dispatch('dms:view-updated', view: $this->view);
    }

    public function render()
    {
        $base = DmsDocument::query()->with(['files'])->withTrashed();

        if ($this->view === 'trash') {
            $base->onlyTrashed();
        } else {
            $base->whereNull('deleted_at');
        }

        if ($this->view === 'archive') {
            $base->where('status', 'Arsip');
        } elseif ($this->view === '' && empty($this->filters['status'])) {
            $base->where('status', 'Aktif');
        }

        if (!empty($this->filters['status'])) {
            $base->whereIn('status', $this->filters['status']);
        }
        if (!empty($this->filters['q'])) {
            $q = $this->filters['q'];
            $base->where(function ($qb) use ($q) {
                $qb->where('doc_no', 'like', "%{$q}%")
                    ->orWhere('title', 'like', "%{$q}%")
                    ->orWhere('type', 'like', "%{$q}%")
                    ->orWhere('tag', 'like', "%{$q}%")
                    ->orWhere('uploader', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }
        if (!empty($this->filters['doc_no'])) {
            $base->where('doc_no', 'like', '%' . $this->filters['doc_no'] . '%');
        }
        if (!empty($this->filters['type'])) {
            $base->whereIn('type', (array)$this->filters['type']);
        }
        if (!empty($this->filters['tag'])) {
            $base->whereIn('tag', (array)$this->filters['tag']);
        }
        if (!empty($this->filters['year_from'])) {
            $base->where('year', '>=', (int)$this->filters['year_from']);
        }
        if (!empty($this->filters['year_to'])) {
            $base->where('year', '<=', (int)$this->filters['year_to']);
        }

        $sortMap = [
            'no' => 'doc_no',
            'name' => 'title',
            'title' => 'title',
            'type' => 'type',
            'year' => 'year',
            'updated' => 'updated_at',
        ];
        $column = $sortMap[$this->sort] ?? 'updated_at';
        $direction = $this->dir === 'asc' ? 'asc' : 'desc';

        $perPage = $this->hasActiveFilter() ? 500 : $this->perPage;
        $documents = $base->orderBy($column, $direction)->paginate($perPage);
        if ($this->view === 'trash') {
            $documents->getCollection()->transform(function ($doc) {
                if ($doc->deleted_at) {
                    $deadline = $doc->deleted_at->copy()->addDays(30);
                    $doc->deletion_deadline = $deadline;
                    $doc->remaining_days = max(0, now()->floatDiffInDays($deadline, false));
                } else {
                    $doc->deletion_deadline = null;
                    $doc->remaining_days = null;
                }
                return $doc;
            });
        }

        return view('livewire.dms-table', [
            'documents' => $documents,
            'isTrash' => $this->view === 'trash',
            'isArchive' => $this->view === 'archive',
            'sort' => $this->sort,
            'dir' => $this->dir,
        ]);
    }

    private function hasActiveFilter(): bool
    {
        return !empty($this->filters['q'])
            || !empty($this->filters['doc_no'])
            || !empty($this->filters['status'])
            || !empty($this->filters['type'])
            || !empty($this->filters['tag'])
            || !empty($this->filters['year_from'])
            || !empty($this->filters['year_to'])
            || $this->view !== '';
    }
}
