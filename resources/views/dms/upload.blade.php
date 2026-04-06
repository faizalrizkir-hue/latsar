@extends('layouts.dashboard-shell')

@section('title', $pageTitle ?? 'Unggah Dokumen')

@push('head')
    <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/dms.css') }}">
    <style>
        .tab-btn{
            border-radius: 12px;
            padding: 8px 14px;
            border:1px solid #d9e2ec;
            background:#f8fafc;
            color:#0f172a;
            font-weight:700;
            box-shadow:0 10px 18px rgba(15,23,42,0.06);
            transition:all .18s ease;
        }
        .tab-btn:hover{border-color:#93c5fd; box-shadow:0 12px 22px rgba(59,130,246,0.18);}
        .tab-btn.active{
            background:linear-gradient(135deg,#2563eb,#0ea5e9);
            color:#fff;
            border-color:rgba(59,130,246,0.8);
            box-shadow:0 12px 26px rgba(37,99,235,0.28);
        }
        .tab-btn.tab-dup{
            border-color:#ef4444;
            color:#b91c1c;
            background:#fee2e2;
            box-shadow:0 10px 20px rgba(239,68,68,0.18);
        }
        .tab-btn.tab-dup.active{
            background:linear-gradient(135deg,#ef4444,#f87171);
            color:#fff;
            box-shadow:0 12px 26px rgba(239,68,68,0.28);
        }
        .tab-btn .tab-close{
            display:inline-grid;
            place-items:center;
            width:22px;height:22px;
            margin-left:6px;
            border-radius:50%;
            background:#f1f5f9;
            border:1px solid #d9e2ec;
            color:#0f172a;
            font-weight:900;
            font-size:12px;
            line-height:1;
            opacity:0.75;
            transition:opacity .15s ease, transform .12s ease, background .15s ease, border-color .15s ease;
        }
        .tab-btn:hover .tab-close{opacity:1; background:#e2e8f0;}
        .tab-btn .tab-close:hover{transform:scale(1.05);}
        .tab-btn.tab-dup .tab-close{background:#fee2e2;border-color:#fca5a5;color:#b91c1c;}
        .doc-tab-bar{display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
        .doc-tab-list{display:flex;gap:10px;flex-wrap:wrap;}
        .doc-tab-hint{color:#64748b;font-size:0.9rem;}
        .doc-pane { display: none; }
        .doc-pane.active { display: block; }
        .opt-list{list-style:none;padding:0;margin:0;max-height:220px;overflow:auto;border:2px solid #c4d4f5;border-radius:12px;}
        .opt-item{padding:10px 12px;cursor:pointer;}
        .opt-item:hover{background:#f1f5f9;}
        .opt-item.active{background:#e7f1ff;font-weight:600;}
        .slide-panel{overflow:hidden;max-height:0;padding:0;border-radius:12px;border:2px solid transparent;transition:max-height .25s ease, padding .25s ease, border-color .25s ease;}
        .slide-panel.open{border-color:#c4d4f5;padding:12px;}
        .detail-card{background:#fff;border-radius:14px;border:3px solid #c4d4f5;}
        .detail-card-wrap{margin-top:24px;}
        .card.dms-card-strong{border:2px solid #b4c6e7 !important; box-shadow:0 16px 32px rgba(15,23,42,0.10);}
        .btn-soft-primary{
            background:#e6f0ff;
            border:1px solid #c2d5ff;
            color:#0f172a;
            font-weight:700;
        }
        .btn-soft-primary:hover{background:#dbe7ff;border-color:#adc7ff;}
        .detail-card .card-header{
            background:linear-gradient(120deg,#f8fbff,#eef2ff);
            border-bottom:1px solid #e2e8f0;
        }
        .link-pill{
            border:1px solid #cbd5e1;
            background:#f8fafc;
            padding:4px 10px;
            border-radius:999px;
            font-size:12px;
            font-weight:600;
            color:#0f172a;
            text-decoration:none;
            transition:all .15s ease;
        }
        .link-pill:hover{border-color:#2563eb;color:#2563eb;background:#eef2ff;text-decoration:none;}
    </style>
@endpush

@section('content')
    <div class="dms-hero mb-3">
        <div class="hero-left w-100">
            <div class="dms-action-bar dms-action-bar-flat w-100">
                <div>
                    <h5 class="mb-1">Unggah Berkas</h5>
                    <p class="mb-0 text-muted">Tambahkan dokumen baru ke Data Management System</p>
                </div>
                <div class="flex-spacer"></div>
                <a href="{{ route('dms.index') }}" class="dms-btn dms-btn-ghost">Kembali</a>
            </div>
        </div>
    </div>

    <div class="card shadow-sm dms-card-lift dms-card-strong">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('dms.store') }}" enctype="multipart/form-data" id="uploadForm">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tahun</label>
                        <input type="number" name="year" class="form-control border-2" required min="2000" max="{{ date('Y')+1 }}" value="{{ date('Y') }}">
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Judul</label>
                        <input type="text" id="mainTitle" name="title" class="form-control border-2" placeholder="Masukkan judul dokumen" required>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-12">
                        <label class="form-label fw-semibold mb-1">Jenis Dokumen</label>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="jenisToggle">Pilih Jenis</button>
                            <div class="text-muted small">Klik tombol untuk membuka pilihan</div>
                        </div>
                        <div id="jenisPanel" class="slide-panel rounded border p-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="small text-uppercase fw-bold text-muted mb-2">Jenis Dokumen</div>
                                    <ul id="typeList" class="opt-list"></ul>
                                </div>
                                <div class="col-md-6">
                                    <div class="small text-uppercase fw-bold text-muted mb-2">Sub Jenis</div>
                                    <ul id="tagList" class="opt-list"></ul>
                                </div>
                            </div>
                            <input type="hidden" name="type" id="typeHidden" required>
                            <input type="hidden" name="tag" id="tagHidden" required>
                        </div>
                    </div>
                </div>

                <div class="mt-4 detail-card-wrap">
                    <div class="card border shadow-sm detail-card dms-card-strong">
                        <div class="card-header bg-white">
                            <div class="d-flex flex-column flex-lg-row align-items-start gap-2">
                                <div class="fw-semibold text-uppercase small mb-0">Detail Berkas</div>
                                <div class="doc-tab-bar">
                                    <div class="doc-tab-list" id="docTabs"></div>
                                    <button type="button" class="btn btn-soft-primary btn-sm" id="addDoc">Tambah Berkas</button>
                                    <div class="doc-tab-hint d-none d-lg-inline">Pilih tab untuk mengisi atau hapus.</div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="docPanes"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('dms.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function(){
    // data map for tags
    const map = @json($typeOptions);
    const typeList = document.getElementById('typeList');
    const tagList = document.getElementById('tagList');
    const typeHidden = document.getElementById('typeHidden');
    const tagHidden = document.getElementById('tagHidden');
    const panel = document.getElementById('jenisPanel');
    const toggleBtn = document.getElementById('jenisToggle');

    // slide panel
    if(panel && toggleBtn){
        panel.dataset.open='0';
        panel.style.maxHeight='0px';
        panel.classList.remove('open');
        panel.style.display='none';

        const open=()=>{
            panel.style.display='block';
            panel.classList.add('open');
            panel.style.maxHeight = panel.scrollHeight + 'px';
            panel.dataset.open='1';
        };
        const close=()=>{
            panel.style.maxHeight = '0px';
            panel.classList.remove('open');
            panel.dataset.open='0';
            panel.addEventListener('transitionend', function handler(){
                if(panel.dataset.open==='0'){
                    panel.style.display='none';
                }
                panel.removeEventListener('transitionend', handler);
            });
        };
        toggleBtn.addEventListener('click', ()=>{
            panel.dataset.open==='1' ? close() : open();
        });
    }

    function renderList(el, items, active, onPick){
        if(!el) return;
        el.innerHTML='';
        items.forEach(val=>{
            const li=document.createElement('li');
            li.className='opt-item'+(val===active?' active':'');
            li.textContent=val;
            li.onclick = ()=> onPick(val);
            el.appendChild(li);
        });
    }

    function pickType(val){
        typeHidden.value = val;
        renderList(typeList, Object.keys(map), val, pickType);
        const tags = map[val] || [];
        const firstTag = tags[0] || '';
        pickTag(firstTag, tags);
    }

    function pickTag(val, list=null){
        const tags = list || (map[typeHidden.value] || []);
        tagHidden.value = val || '';
        renderList(tagList, tags, val, (v)=>pickTag(v, tags));
    }

    // init dropdown values
    const initialType = Object.keys(map)[0] || '';
    pickType(initialType);

    const existing = @json($existingDocNos ?? []);
    const docTabs = document.getElementById('docTabs');
    const docPanes = document.getElementById('docPanes');
    const addBtn = document.getElementById('addDoc');
    const mainTitle = document.getElementById('mainTitle');

    let index = 0;

    function renumberTabs(){
        Array.from(docTabs.children).forEach((btn,i)=>{
            btn.innerHTML = `Berkas ${i+1} <span class="ms-1 text-danger fw-bold tab-close" data-close>&times;</span>`;
        });
    }

    function addDoc(initial=false){
        const id = index++;
        const tab = document.createElement('button');
        tab.type='button';
        tab.className='btn btn-sm tab-btn btn-outline-primary';
        tab.dataset.target = `doc-${id}`;
        tab.innerHTML = `Berkas ${docTabs.children.length+1} <span class="ms-1 text-danger fw-bold tab-close" data-close>&times;</span>`;
        tab.addEventListener('click', (e)=>{
            if(e.target && e.target.hasAttribute('data-close')){
                e.stopPropagation();
                removeDoc(id, tab, pane);
            } else {
                activate(tab);
            }
        });
        docTabs.appendChild(tab);

        const pane = document.createElement('div');
        pane.className='doc-pane';
        pane.id = `doc-${id}`;
        pane.innerHTML = `
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">No Berkas</label>
                    <input name="doc_no[]" class="form-control border-2 doc-no" placeholder="e-0001/PA.01.01" required>
                    <div class="text-danger small mt-1 warn"></div>
                </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                            <span>Nama Berkas</span>
                            <button type="button" class="link-pill" data-fill-title>Samakan Judul</button>
                        </label>
                        <input name="name[]" class="form-control border-2 doc-name" required>
                    </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Upload Berkas</label>
                    <input type="file" name="files[]" class="form-control border-2" required>
                    <div class="text-muted small mt-1">Ukuran maks 5 MB per berkas.</div>
                </div>
            </div>
        `;
        docPanes.appendChild(pane);

        if(!initial){
            activate(tab);
        }else if(docTabs.children.length === 1){
            activate(tab);
        }
        bindDocValidation();
        renumberTabs();
    }

    function activate(tab){
        const target = tab.dataset.target;
        docTabs.querySelectorAll('button').forEach(t=>t.classList.remove('active'));
        tab.classList.add('active');
        docPanes.querySelectorAll('.doc-pane').forEach(p=>{
            p.classList.toggle('active', p.id === target);
        });
        // tidak lagi autofill nama berkas dari mainName
    }

    function removeDoc(id, tabEl, paneEl){
        if(docTabs.children.length === 1) return; // jangan hapus terakhir
        tabEl.remove();
        paneEl.remove();
        renumberTabs();
        const first = docTabs.querySelector('button');
        if(first) activate(first);
    }

    function bindDocValidation(){
        const inputs = Array.from(docPanes.querySelectorAll('.doc-no'));
        inputs.forEach(inp=>{
            inp.oninput = checkDuplicates;
        });
    }

    function checkDuplicates(){
        const inputs = Array.from(docPanes.querySelectorAll('.doc-no'));
        const values = inputs.map(i=> (i.value||'').trim());
        const paneHasDup = new Map();
        inputs.forEach((inp, idx)=>{
            const val = values[idx];
            const warn = inp.parentElement.querySelector('.warn');
            let msg = '';
            let dup = false;
            if(val){
                const dupLocal = values.filter(v=>v===val).length > 1;
                const dupExisting = existing.includes(val);
                dup = dupLocal || dupExisting;
                if(dup){
                    msg = 'Nomor dokumen sudah terpakai. Gunakan nomor lain.';
                }
            }
            warn.textContent = msg;
            inp.classList.toggle('is-invalid', dup);
            const pane = inp.closest('.doc-pane');
            if(pane){
                paneHasDup.set(pane.id, paneHasDup.get(pane.id) || dup);
            }
        });
        docTabs.querySelectorAll('button').forEach(btn=>{
            const bad = paneHasDup.get(btn.dataset.target);
            btn.classList.toggle('tab-dup', !!bad);
        });
    }

    addBtn.addEventListener('click', ()=>addDoc());
    addDoc(true);

    // set nama berkas = judul dokumen
    docPanes.addEventListener('click', (e)=>{
        const btn = e.target.closest('[data-fill-title]');
        if(!btn) return;
        const pane = btn.closest('.doc-pane');
        const input = pane ? pane.querySelector('.doc-name') : null;
        if(input && mainTitle){
            input.value = mainTitle.value || '';
            input.focus();
        }
    });

    // mainTitle disimpan untuk referensi manual, tanpa autofill ke tab
})();
</script>
@endpush

