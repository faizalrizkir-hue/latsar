<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DmsDocument;
use Illuminate\Support\Facades\Session;

class DmsTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['setSearch' => 'setSearch', 'dms:set-filters' => 'setFilters'];

    public array $filters = [];
    public string $sort = 'updated';
    public string $dir = 'desc';
    public string $view = '';
    public int $perPage = 10;

    protected $updatesQueryString = ['sort', 'dir', 'page', 'perPage'];

    public function mount(array $filters = [], string $view = ''): void
    {
        $defaults = [
            'q' => '',
            'doc_no' => '',
            'status' => [],
            'type' => [],
            'tag' => [],
            'year_from' => null,
            'year_to' => null,
        ];
        $this->filters = array_merge($defaults, $filters);
        $this->view = $view;
        $this->sort = $filters['sort'] ?? 'updated';
        $this->dir = strtolower($filters['dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
    }

    public function updatingFilters(): void
    {
        $this->resetPage();
    }

    public function updated($name, $value): void
    {
        if ($name === 'filters.q') {
            $this->resetPage();
        }
    }

    public function setSearch($payload = ''): void
    {
        $value = is_array($payload) && array_key_exists('value', $payload) ? $payload['value'] : $payload;
        $this->filters['q'] = (string)$value;
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

        return view('livewire.dms-table', [
            'documents' => $documents,
            'isTrash' => $this->view === 'trash',
        ]);
    }

    public function setFilters(array $payload = []): void
    {
        foreach (['q','doc_no','status','type','tag','year_from','year_to'] as $key) {
            if (array_key_exists($key, $payload)) {
                $this->filters[$key] = $payload[$key];
            }
        }
        $this->resetPage();
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
