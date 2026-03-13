@php
    $map = [
        'Manajemen Pengawasan' => [
            'Surat Tugas',
            'Laporan Hasil Pengawasan (LHP)',
            'Program Kerja Pengawasan Tahunan (PKPT)',
            'Tanda Bukti',
            'Telaah Sejawat',
        ],
        'Sumber Daya Manusia' => ['Dokumen SDM'],
        'Keuangan' => ['Dokumen Keuangan'],
        'Pemanfaatan Sistem Informasi (SI)' => ['Dokumen Sistem Informasi (SI)'],
        'Pedoman/Kebijakan' => ['Dokumen Pedoman/Kebijakan'],
        'Lainnya' => ['Dokumen Lainnya'],
    ];
@endphp

{{-- Upload modal --}}
<div class="dms-modal-overlay" id="dmsUploadOverlay" data-close-upload style="display:none;"></div>
<div class="dms-modal" id="dmsUploadModal" role="dialog" aria-modal="true" aria-labelledby="uploadTitle" style="display:none;">
    <div class="dms-modal-shell">
        <div class="dms-modal-header modal-gradient">
            <h5 id="uploadTitle" class="mb-0 text-white">Unggah Berkas</h5>
        <button type="button" class="modal-close modal-close-danger text-white" data-close-upload aria-label="Tutup" onclick="window.dmsUploadClose && window.dmsUploadClose();">&times;</button>
        </div>
        <form class="dms-modal-body" action="{{ route('dms.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="status" value="Aktif">
            <div class="modal-section">
                <label class="modal-label">TAHUN *</label>
                <input type="number" name="year" class="form-control dms-input" min="2000" max="{{ date('Y')+1 }}" required>
                <div class="modal-help">Tuliskan informasi mengenai tahun berkas.</div>
            </div>
            <div class="modal-section">
                <label class="modal-label">JENIS BERKAS *</label>
                <select name="type" id="jenisDokumen" class="form-select dms-select" required>
                    <option value="">Pilih jenis berkas</option>
                    @foreach(array_keys($map) as $key)
                        <option value="{{ $key }}">{{ $key }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-section">
                <label class="modal-label">DESKRIPSI BERKAS</label>
                <textarea name="description" rows="3" class="form-control dms-textarea" placeholder="Tambahkan deskripsi singkat"></textarea>
            </div>
            <div class="modal-section">
                <label class="modal-label">DETAIL BERKAS *</label>
                <div class="pill-tabs" id="pillTabs">
                    <span class="pill active" data-pill="1">Berkas 1</span>
                    <button type="button" class="pill pill-add" id="addFileRow" aria-label="Tambah lampiran">+</button>
                </div>
                <div id="fileList">
                    <div class="detail-card" data-file-row="1">
                        <div class="form-group mt-2">
                            <label class="form-label small fw-semibold">Upload Berkas 1</label>
                            <input type="file" name="files[]" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-help">Ukuran maksimal 5 megabytes per berkas.</div>
            </div>
            <div class="dms-modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-close-upload>Kembali</button>
                <button class="btn btn-success dms-upload-btn" type="submit">⬆ Unggah</button>
            </div>
        </form>
    </div>
</div>

<template id="fileRowTemplate">
    <div class="detail-card" data-file-row="{index}">
        <div class="form-group mt-2">
            <label class="form-label small fw-semibold">Upload Berkas {index}</label>
            <input type="file" name="files[]" class="form-control" required>
        </div>
    </div>
</template>

@push('scripts')
<script>
(function(){
    if(window.__dmsModalInit) return;
    window.__dmsModalInit=true;
    function getNodes(){
        return {
            modal:document.getElementById('dmsUploadModal'),
            overlay:document.getElementById('dmsUploadOverlay'),
            blurTargets:document.querySelectorAll('.sidenav, .headnav, .topbar, .content-panel, .legacy-body')
        };
    }
    function showModal(){
        const {modal,overlay,blurTargets}=getNodes();
        if(!modal||!overlay) return;
        modal.classList.remove('closing');
        overlay.classList.remove('closing');
        modal.style.display='flex';
        overlay.style.display='block';
        modal.classList.add('show');
        overlay.classList.add('show');
        document.body.classList.add('no-scroll');
        blurTargets.forEach(el=>el.classList.add('blurred'));
    }
    function hideModal(){
        const {modal,overlay,blurTargets}=getNodes();
        if(!modal||!overlay) return;
        modal.classList.add('closing');
        overlay.classList.add('closing');
        modal.classList.remove('show');
        overlay.classList.remove('show');
        setTimeout(()=>{
            modal.style.display='none';
            overlay.style.display='none';
            modal.classList.remove('closing');
            overlay.classList.remove('closing');
        },180);
        document.body.classList.remove('no-scroll');
        blurTargets.forEach(el=>el.classList.remove('blurred'));
    }
    window.dmsUploadShow = showModal;
    window.dmsUploadClose = hideModal;
    document.addEventListener('click',(e)=>{
        const openTrigger=e.target.closest('[data-open-upload],[data-open-upload-global]');
        if(openTrigger){
            e.preventDefault();
            showModal();
            return;
        }
        const closeTrigger=e.target.closest('[data-close-upload]');
        if(closeTrigger){
            e.preventDefault();
            hideModal();
        }
    });
    document.addEventListener('keydown',(e)=>{ if(e.key==='Escape') hideModal();});
    const overlay=getNodes().overlay;
    overlay?.addEventListener('click',hideModal);

    function initUploadModal(){
        const jenisSelect=document.getElementById('jenisDokumen');
        const subSelect=document.getElementById('subJenisDokumen');
        if(!jenisSelect){
            setTimeout(initUploadModal,150);
            return;
        }
        const dataMap={
            'Manajemen Pengawasan': [
                'Surat Tugas',
                'Laporan Hasil Pengawasan (LHP)',
                'Program Kerja Pengawasan Tahunan (PKPT)',
                'Tanda Bukti',
                'Telaah Sejawat',
            ],
            'Sumber Daya Manusia': ['Dokumen SDM'],
            'Keuangan': ['Dokumen Keuangan'],
            'Pemanfaatan Sistem Informasi (SI)': ['Dokumen Sistem Informasi (SI)'],
            'Pedoman/Kebijakan': ['Dokumen Pedoman/Kebijakan'],
            'Lainnya': ['Dokumen Lainnya'],
        };
        if(subSelect){
            function fillSub(){
                const main=jenisSelect.value;
                subSelect.innerHTML='';
                (dataMap[main]||[]).forEach(opt=>{
                    const o=document.createElement('option');
                    o.value=opt;
                    o.textContent=opt;
                    subSelect.appendChild(o);
                });
            }
            jenisSelect.addEventListener('change',fillSub);
            fillSub();
        }

        let fileCount=1;
        const fileList=document.getElementById('fileList');
        const pillTabs=document.getElementById('pillTabs');
        const addBtn=document.getElementById('addFileRow');
        const template=document.getElementById('fileRowTemplate')?.innerHTML || '';
        function addFileRow(){
            fileCount+=1;
            const html=template.replaceAll('{index}', fileCount);
            const wrapper=document.createElement('div');
            wrapper.innerHTML=html.trim();
            const node=wrapper.firstChild;
            fileList.appendChild(node);
            const pill=document.createElement('span');
            pill.className='pill';
            pill.dataset.pill=String(fileCount);
            pill.textContent='Berkas '+fileCount;
            pillTabs.insertBefore(pill, addBtn);
        }
        addBtn?.addEventListener('click',(e)=>{e.preventDefault();addFileRow();});
    }
    if(document.readyState==='loading'){
        document.addEventListener('DOMContentLoaded', initUploadModal);
    }else{
        initUploadModal();
    }
})();
</script>
@endpush
