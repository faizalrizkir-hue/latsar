@extends('layouts.dashboard-shell')

@section('title', $pageTitle ?? 'DMS')

@push('head')
    <link rel="stylesheet" href="{{ asset('css/dms.css') }}">
@endpush

@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
    $view = $filters['view'] ?? '';
@endphp

@section('content')
    <div class="dms-hero mb-3">
        <div class="hero-left">
            <div class="dms-action-bar dms-action-bar-flat">
                <a class="dms-btn dms-btn-primary" href="{{ route('dms.create') }}">
                    <span class="icon">⬆</span> Unggah Berkas
                </a>
                <button class="dms-btn dms-btn-ghost" type="button" id="btnFilterToggle">
                    <span class="icon">⚙️</span> Filter
                </button>
                <div class="flex-spacer"></div>
            </div>
        </div>
        <div class="hero-right">
            <div class="dms-chip chip-active">Aktif <span class="chip-count" data-count-target="{{ $counts['active'] }}">{{ $counts['active'] }}</span></div>
            <div class="dms-chip chip-archive">Arsip <span class="chip-count" data-count-target="{{ $counts['archive'] }}">{{ $counts['archive'] }}</span></div>
            <div class="dms-chip chip-trash">Dihapus <span class="chip-count" data-count-target="{{ $counts['trash'] }}">{{ $counts['trash'] }}</span></div>
            <div class="dms-chip chip-total">Total <span class="chip-count" data-count-target="{{ $counts['active'] + $counts['archive'] + $counts['trash'] }}">{{ $counts['active'] + $counts['archive'] + $counts['trash'] }}</span></div>
        </div>
    </div>

    <div class="card shadow-sm dms-filter-wrap collapsed dms-card-lift" id="dms-filter">
        <form id="dmsFilterForm" class="card-body dms-filter-card" method="GET" action="{{ route('dms.index') }}">
            <div class="dms-field-row">
                <div class="form-group">
                    <label class="form-label small fw-semibold">Berkas</label>
                    <input type="text" class="form-control dms-input" id="dmsFilterSearch" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Ketik untuk cari judul/tag/deskripsi">
                </div>
                <div class="form-group">
                    <label class="form-label small fw-semibold">Nomor Berkas</label>
                    <input type="text" class="form-control dms-input" id="dmsFilterDocNo" name="doc_no" value="{{ $filters['doc_no'] ?? '' }}" placeholder="Ketik nomor dokumen/berkas">
                </div>
                <div class="form-group">
                    <label class="form-label small fw-semibold">Jenis Berkas</label>
                    <select class="form-select dms-select" id="dmsFilterType" name="type[]">
                        <option value="">Semua jenis</option>
                        @foreach(array_keys($typeOptions ?? []) as $type)
                            <option value="{{ $type }}" @selected(in_array($type, $filters['type'] ?? []))>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label small fw-semibold">Sub Jenis</label>
                    <select class="form-select dms-select" id="dmsFilterTag" name="tag[]">
                        <option value="">Semua sub jenis</option>
                    </select>
                </div>
            </div>

            <div class="dms-field-row align-center">
                <div class="form-group w-100">
                    <label class="form-label small fw-semibold">Tahun</label>
                    <div class="dms-year-range">
                        <input type="number" class="form-control dms-input" name="year_from" value="{{ $filters['year_from'] ?? '' }}" placeholder="Dari">
                        <span class="dms-sep">s/d</span>
                        <input type="number" class="form-control dms-input" name="year_to" value="{{ $filters['year_to'] ?? '' }}" placeholder="Sampai">
                    </div>
                </div>
                <div class="form-group dms-actions">
                    <input type="hidden" name="view" value="{{ $view }}" data-dms-view-field>
                    <button class="btn btn-outline-secondary" type="button" id="dmsFilterResetBtn">Reset</button>
                </div>
            </div>
        </form>
    </div>
@push('scripts')
<script>
    (function(){
        const form = document.getElementById('dmsFilterForm');
        const searchInput = document.getElementById('dmsFilterSearch');
        const docNoInput = document.getElementById('dmsFilterDocNo');
        const typeSelect = document.getElementById('dmsFilterType');
        const tagSelect = document.getElementById('dmsFilterTag');
        const resetBtn = document.getElementById('dmsFilterResetBtn');
        const yearFromInput = form?.querySelector('input[name="year_from"]');
        const yearToInput = form?.querySelector('input[name="year_to"]');
        const viewField = form?.querySelector('[data-dms-view-field]');
        const typeMap = @json($typeOptions ?? []);
        const initialType = (@json($filters['type'][0] ?? '')) || '';
        const initialTag = (@json($filters['tag'][0] ?? '')) || '';

        function rebuildTags(selectedType, selectedTag=''){
            if(!tagSelect) return;
            tagSelect.innerHTML = '<option value=\"\">Semua sub jenis</option>';
            const tags = typeMap[selectedType] || [];
            tags.forEach(tg=>{
                const opt = document.createElement('option');
                opt.value = tg;
                opt.textContent = tg;
                if(selectedTag && selectedTag.toLowerCase() === tg.toLowerCase()){
                    opt.selected = true;
                }
                tagSelect.appendChild(opt);
            });
        }

        if(typeSelect && initialType){
            typeSelect.value = initialType;
        }
        if(typeSelect){
            rebuildTags(typeSelect.value || '', initialTag);
        }

        const animateSummaryCounts = () => {
            const counters = document.querySelectorAll('.chip-count[data-count-target]');
            if (!counters.length) return;

            const prefersReducedMotion = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
            const easeOutCubic = (t) => 1 - Math.pow(1 - t, 3);

            counters.forEach((counter) => {
                const targetRaw = Number(counter.getAttribute('data-count-target') || '0');
                const target = Number.isFinite(targetRaw) ? Math.max(0, Math.round(targetRaw)) : 0;

                if (prefersReducedMotion || target <= 0) {
                    counter.textContent = String(target);
                    return;
                }

                const durationMs = 900;
                const startAt = performance.now();
                counter.textContent = '0';

                const animate = (now) => {
                    const elapsed = now - startAt;
                    const progress = Math.min(elapsed / durationMs, 1);
                    const currentValue = Math.round(target * easeOutCubic(progress));
                    counter.textContent = String(currentValue);

                    if (progress < 1) {
                        window.requestAnimationFrame(animate);
                    }
                };

                window.requestAnimationFrame(animate);
            });
        };

        animateSummaryCounts();

        const filterRows = () => {
            const tableWrap = document.getElementById('dmsTableWrap');
            if(!tableWrap) return;
            const q = (searchInput?.value || '').toLowerCase();
            const docq = (docNoInput?.value || '').toLowerCase();
            const typeq = (typeSelect?.value || '').toLowerCase();
            const tagq = (tagSelect?.value || '').toLowerCase();
            const rows = tableWrap.querySelectorAll('table tbody tr.dms-hover-row');
            let any = false;
            rows.forEach(row => {
                const hay = (row.dataset.search || row.innerText || '').toLowerCase();
                const docHay = (row.dataset.docNo || '').toLowerCase();
                const typeHay = (row.dataset.type || '').toLowerCase();
                const tagHay = (row.dataset.tag || '').toLowerCase();
                const typeOk = typeq === '' || typeHay === typeq;
                const tagOk = tagq === '' || tagHay === tagq;
                const match = hay.indexOf(q) !== -1 && docHay.indexOf(docq) !== -1 && typeOk && tagOk;
                row.classList.toggle('d-none', !match);
                if(match) any = true;
            });
            const emptyRow = tableWrap.querySelector('tr[data-empty-row]');
            if(emptyRow){
                emptyRow.classList.toggle('d-none', any);
            }
        };

        const debounce = (fn, wait = 250) => {
            let t;
            return (...args) => {
                clearTimeout(t);
                t = setTimeout(() => fn(...args), wait);
            };
        };

        const buildPayload = () => ({
            q: (searchInput?.value || '').trim(),
            doc_no: (docNoInput?.value || '').trim(),
            type: typeSelect?.value ? [typeSelect.value] : [],
            tag: tagSelect?.value ? [tagSelect.value] : [],
            year_from: (yearFromInput?.value || '').trim(),
            year_to: (yearToInput?.value || '').trim(),
        });

        const getDmsComponent = () => {
            const tableRoot = document.querySelector('[data-dms-table]');
            const componentId = tableRoot?.getAttribute('wire:id');
            if (!componentId || !window.Livewire?.find) return null;
            return window.Livewire.find(componentId);
        };

        const pushToLivewire = () => {
            const payload = buildPayload();
            const component = getDmsComponent();
            if (component?.call) {
                component.call('setFilters', payload);
                return;
            }
            if (window.Livewire?.dispatch) {
                window.Livewire.dispatch('dms:set-filters', { payload });
            }
        };

        const runFilters = () => {
            filterRows();        // instant on current page
            pushToLivewire();    // ask Livewire to refetch without reload
        };
        const debouncedRunFilters = debounce(runFilters, 300);

        const clearFilters = () => {
            if(searchInput) searchInput.value = '';
            if(docNoInput) docNoInput.value = '';
            if(yearFromInput) yearFromInput.value = '';
            if(yearToInput) yearToInput.value = '';
            if(typeSelect) {
                typeSelect.value = '';
                rebuildTags('');
            } else if(tagSelect) {
                tagSelect.innerHTML = '<option value=\"\">Semua sub jenis</option>';
            }
            if(tagSelect) tagSelect.value = '';
        };

        form?.addEventListener('submit', (e) => {
            e.preventDefault();
            runFilters();
        });

        searchInput?.addEventListener('input', debouncedRunFilters);
        docNoInput?.addEventListener('input', debouncedRunFilters);
        typeSelect?.addEventListener('change', ()=>{
            rebuildTags(typeSelect.value || '');
            debouncedRunFilters();
        });
        tagSelect?.addEventListener('change', debouncedRunFilters);
        yearFromInput?.addEventListener('input', debouncedRunFilters);
        yearToInput?.addEventListener('input', debouncedRunFilters);
        resetBtn?.addEventListener('click', () => {
            clearFilters();
            runFilters();
        });
        filterRows(); // initial state when page first loads

        const onLivewireReady = (cb) => {
            if (window.Livewire) {
                cb(window.Livewire);
            } else {
                document.addEventListener('livewire:init', () => cb(window.Livewire));
            }
        };

        onLivewireReady((LW) => {
            // keep client-side hide/show in sync after Livewire re-render
            LW.hook?.('message.processed', () => filterRows());
            LW.on?.('dms:view-updated', (payload) => {
                if(viewField){
                    viewField.value = payload?.view || '';
                }
            });
        });

        const wrap=document.getElementById('dms-filter');
        const btn=document.getElementById('btnFilterToggle');
        if(!wrap||!btn)return;
        const openPanel = () => {
            wrap.classList.remove('closing');
            wrap.classList.add('open');
            const fullHeight = wrap.scrollHeight;
            wrap.style.height = fullHeight + 'px';
            wrap.addEventListener('transitionend', function handler(e){
                if(e.propertyName === 'height'){
                    wrap.style.height = 'auto';
                    wrap.removeEventListener('transitionend', handler);
                }
            });
            btn.classList.add('active');
            btn.setAttribute('aria-pressed','true');
        };
        const closePanel = () => {
            const currentHeight = wrap.scrollHeight;
            wrap.style.height = currentHeight + 'px';
            requestAnimationFrame(()=>{
                wrap.classList.add('closing');
                wrap.classList.remove('open');
                wrap.style.height = '0px';
            });
            btn.classList.remove('active');
            btn.setAttribute('aria-pressed','false');
        };
        btn.addEventListener('click',()=>{
            const isOpen = wrap.classList.contains('open');
            if(isOpen){
                closePanel();
            } else {
                // reset to actual height before opening
                wrap.style.height = '0px';
                requestAnimationFrame(()=>openPanel());
            }
        });
        // init collapsed
        wrap.style.height = '0px';

        wrap.addEventListener('transitionend', function(e){
            if(e.propertyName !== 'height') return;
            if(!wrap.classList.contains('open')){
                wrap.classList.remove('closing');
                wrap.style.height = '0px';
            }
        });
    })();

    (function(){
        if (window.__dmsActionModalInit) return;
        window.__dmsActionModalInit = true;

        const transitionMs = 180;
        let pendingForm = null;
        let lastTrigger = null;
        let closeTimer = null;

        const getModal = () => document.getElementById('dmsActionConfirmModal');
        const getTitleEl = () => document.getElementById('dmsActionConfirmModalTitle');
        const getMessageEl = () => document.getElementById('dmsActionConfirmModalMessage');
        const getConfirmBtn = () => document.getElementById('dmsActionConfirmModalConfirm');
        const getViewportUiScale = () => {
            const zoomRaw = getComputedStyle(document.body).zoom;
            const zoom = parseFloat(zoomRaw || '1');
            return Number.isFinite(zoom) && zoom > 0 ? zoom : 1;
        };

        const syncModalToViewport = () => {
            const modal = getModal();
            if (!modal || !modal.classList.contains('is-open')) return;

            const scale = getViewportUiScale();
            modal.style.top = `${Math.round(window.scrollY / scale)}px`;
            modal.style.left = `${Math.round(window.scrollX / scale)}px`;
            modal.style.width = `${Math.ceil(window.innerWidth / scale)}px`;
            modal.style.height = `${Math.ceil(window.innerHeight / scale)}px`;
        };

        const clearModalViewportStyles = () => {
            const modal = getModal();
            if (!modal) return;

            modal.style.removeProperty('top');
            modal.style.removeProperty('left');
            modal.style.removeProperty('width');
            modal.style.removeProperty('height');
        };

        const closeModal = () => {
            const modal = getModal();
            const confirmBtn = getConfirmBtn();

            pendingForm = null;

            if (modal) {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                modal.removeAttribute('data-kind');
                if (closeTimer) {
                    clearTimeout(closeTimer);
                }
                closeTimer = setTimeout(() => {
                    if (!modal.classList.contains('is-open')) {
                        modal.setAttribute('hidden', 'hidden');
                        clearModalViewportStyles();
                    }
                    closeTimer = null;
                }, transitionMs);
            }

            if (confirmBtn) {
                confirmBtn.classList.remove('is-danger', 'is-warning');
            }

            lastTrigger?.focus?.({ preventScroll: true });
            lastTrigger = null;
        };

        const openModal = (trigger) => {
            const modal = getModal();
            const titleEl = getTitleEl();
            const messageEl = getMessageEl();
            const confirmBtn = getConfirmBtn();

            pendingForm = trigger.closest('form');
            if (!modal || !titleEl || !messageEl || !confirmBtn || !pendingForm) return;
            if (closeTimer) {
                clearTimeout(closeTimer);
                closeTimer = null;
            }

            lastTrigger = trigger;

            const kind = (trigger.getAttribute('data-dms-confirm-kind') || '').trim();
            titleEl.textContent = trigger.getAttribute('data-dms-confirm-title') || 'Konfirmasi';
            messageEl.textContent = trigger.getAttribute('data-dms-confirm-message') || 'Lanjutkan tindakan ini?';
            confirmBtn.textContent = trigger.getAttribute('data-dms-confirm-label') || 'Lanjutkan';
            confirmBtn.classList.toggle('is-danger', kind === 'delete');
            confirmBtn.classList.toggle('is-warning', kind === 'archive');

            modal.setAttribute('data-kind', kind || 'default');
            modal.removeAttribute('hidden');
            modal.setAttribute('aria-hidden', 'false');
            requestAnimationFrame(() => {
                modal.classList.add('is-open');
                syncModalToViewport();
            });
            requestAnimationFrame(() => {
                getConfirmBtn()?.focus({ preventScroll: true });
            });
        };

        document.addEventListener('click', (event) => {
            const trigger = event.target.closest('[data-dms-confirm-trigger]');
            if (trigger) {
                event.preventDefault();
                openModal(trigger);
                return;
            }

            const confirmTrigger = event.target.closest('#dmsActionConfirmModalConfirm');
            if (confirmTrigger) {
                event.preventDefault();
                const formToSubmit = pendingForm;
                closeModal();
                formToSubmit?.submit();
                return;
            }

            if (event.target.closest('[data-dms-confirm-close]')) {
                event.preventDefault();
                closeModal();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && getModal()?.classList.contains('is-open')) {
                closeModal();
            }
        });

        document.addEventListener('livewire:navigated', closeModal);
        window.addEventListener('scroll', syncModalToViewport, { passive: true });
        window.addEventListener('resize', syncModalToViewport);
        window.addEventListener('pageshow', syncModalToViewport);
    })();

</script>
@endpush


    <livewire:dms-table :filters="$filters" :view="$view" />
@endsection

@push('global-modals')
<div class="dms-confirm-modal" id="dmsActionConfirmModal" hidden aria-hidden="true">
    <div class="dms-confirm-modal__backdrop" data-dms-confirm-close></div>
    <div class="dms-confirm-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="dmsActionConfirmModalTitle">
        <div class="dms-confirm-modal__eyebrow">Konfirmasi Tindakan</div>
        <h3 class="dms-confirm-modal__title" id="dmsActionConfirmModalTitle">Konfirmasi</h3>
        <p class="dms-confirm-modal__body" id="dmsActionConfirmModalMessage">Lanjutkan tindakan ini?</p>
        <div class="dms-confirm-modal__actions">
            <button type="button" class="btn dms-confirm-modal__cancel" data-dms-confirm-close>Batal</button>
            <button type="button" class="btn dms-confirm-modal__confirm" id="dmsActionConfirmModalConfirm">Lanjutkan</button>
        </div>
    </div>
</div>
@endpush


