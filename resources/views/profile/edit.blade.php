@extends('layouts.dashboard-shell')

@push('head')
    <link rel="stylesheet" href="/css/profile.css">
@endpush

@section('content')
    <div class="row g-4 profile-page">
        <div class="col-lg-7">
            <div class="card gradient-card shadow-lg">
                <div class="card-body">
                    <div class="card-hero d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Edit Profil</h5>
                            <small>Perbarui nama tampilan, password, atau foto profil.</small>
                        </div>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger bg-opacity-75">
                            <strong>Periksa lagi:</strong>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="profileForm" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="row g-3">
                        @csrf
                        <div class="col-12">
                            <label for="displayName" class="form-label">Nama tampilan</label>
                            <input type="text" id="displayName" name="display_name"
                                   value="{{ old('display_name', $account?->display_name ?? ($user['display_name'] ?? '')) }}"
                                   class="form-control @error('display_name') is-invalid @enderror" required>
                            @error('display_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="newPassword" class="form-label">Password baru</label>
                            <input type="password" id="newPassword" name="new_password"
                                   class="form-control @error('new_password') is-invalid @enderror"
                                   placeholder="Biarkan kosong jika tidak diubah">
                            @error('new_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="confirmPassword" class="form-label">Konfirmasi password</label>
                            <input type="password" id="confirmPassword" name="confirm_password"
                                   class="form-control @error('confirm_password') is-invalid @enderror">
                            @error('confirm_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label for="profilePhoto" class="form-label">Foto profil</label>
                            <div class="file-picker">
                                <input type="file" id="profilePhoto" name="profile_photo"
                                       class="@error('profile_photo') is-invalid @enderror"
                                       accept="image/*">
                                <label for="profilePhoto" aria-label="Pilih file foto">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 7h4l2-3h6l2 3h4v13H3z"/><path d="M12 11a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z"/>
                                    </svg>
                                    Unggah foto
                                </label>
                                <span class="file-name" id="fileNameLabel">Tidak ada file yang dipilih</span>
                            </div>
                            @error('profile_photo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            <div class="form-text">Maks 2 MB. Format gambar (JPG/PNG).</div>

                            <div class="d-flex align-items-center gap-3 mt-3">
                                <div class="preview-circle" id="avatarPreview">
                                    @if(($account?->profile_photo ?? '') !== '')
                                        <img src="{{ asset('uploads/'.$account->profile_photo) }}" alt="Foto profil" id="avatarPreviewImg" style="width:100%;height:100%;object-fit:cover;">
                                    @else
                                        <span>{{ strtoupper(substr(preg_replace('/\s+/', '', $user['display_name'] ?? 'US') ?: 'US',0,2)) }}</span>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="text-white-75 small">Foto preview</div>
                                    <input type="hidden" name="remove_photo" id="removePhotoField" value="0">
                                    <button type="button" id="removePhotoBtn" class="btn btn-outline-danger btn-sm mt-2">
                                        Hapus foto
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-cta">Simpan perubahan</button>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card card-soft shadow-sm h-100">
                <div class="card-body">
                    <div class="card-hero card-soft-hero d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Info Akun</h5>
                            <small>Rangkuman kredensial dan status login.</small>
                        </div>
                    </div>
                    <dl class="row mb-0 text-white-75">
                        <dt class="col-5 text-muted">Username</dt>
                        <dd class="col-7">{{ $account?->username ?? ($user['username'] ?? '-') }}</dd>
                        <dt class="col-5 text-muted">Nama</dt>
                        <dd class="col-7">{{ $account?->display_name ?? ($user['display_name'] ?? '-') }}</dd>
                        <dt class="col-5 text-muted">Role</dt>
                        <dd class="col-7">{{ $account?->role_label ?? ($user['role_label'] ?? \App\Models\Account::roleLabel($account?->role ?? ($user['role'] ?? null))) }}</dd>
                        <dt class="col-5 text-muted">Status</dt>
                        <dd class="col-7">
                            @if($account?->active ?? true)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Nonaktif</span>
                            @endif
                        </dd>
                        <dt class="col-5 text-muted">IP terakhir</dt>
                        <dd class="col-7">{{ $account?->last_login_ip ?? '-' }}</dd>
                        <dt class="col-5 text-muted">Device terakhir</dt>
                        <dd class="col-7" style="word-break: break-word;">{{ $account?->last_login_device ?? '-' }}</dd>
                        <dt class="col-5 text-muted">Terakhir diperbarui</dt>
                        <dd class="col-7">{{ $account?->updated_at?->timezone('Asia/Jakarta')->format('d M Y H:i') ?? '-' }}</dd>
                    </dl>

                    <div class="profile-team-summary">
                        <div class="profile-team-summary__head">
                            <div>
                                <h6 class="mb-1">Informasi Tim</h6>
                                <small>Ringkasan penugasan pada element yang ditunjuk administrator.</small>
                            </div>
                        </div>

                        @forelse($teamAssignmentSummary as $teamAssignment)
                            <div class="profile-team-card">
                                <div class="profile-team-card__top">
                                    <span class="profile-team-role {{ $teamAssignment['position_class'] }}">{{ $teamAssignment['position_label'] }}</span>
                                    <span class="profile-team-count">{{ $teamAssignment['member_count_text'] }}</span>
                                </div>
                                <div class="profile-team-card__title">{{ $teamAssignment['element_label'] }}</div>
                                <div class="profile-team-card__meta">{{ $teamAssignment['summary_label'] }}</div>

                                @if(!empty($teamAssignment['people']))
                                    <div class="profile-team-card__people">
                                        <span class="profile-team-card__people-label">{{ $teamAssignment['people_label'] }}</span>
                                        <span class="profile-team-card__people-text">{{ implode(', ', $teamAssignment['people']) }}</span>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="profile-team-empty">
                                Belum ada penugasan tim element untuk akun ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const fileInput = document.getElementById('profilePhoto');
    const preview = document.getElementById('avatarPreview');
    const displayNameInput = document.getElementById('displayName');
    const removePhotoField = document.getElementById('removePhotoField');
    const removePhotoBtn = document.getElementById('removePhotoBtn');
    const fileNameLabel = document.getElementById('fileNameLabel');
    const defaultFileLabel = 'Tidak ada file yang dipilih';

    const buildPreviewInitials = () => {
        const name = (displayNameInput?.value || '').trim();
        const compactName = name.replace(/\s+/g, '');

        if (!compactName) {
            return 'US';
        }

        return compactName.slice(0, 2).toUpperCase();
    };

    const renderPreviewPlaceholder = () => {
        if (!preview) return;
        preview.innerHTML = '';
        const placeholder = document.createElement('span');
        placeholder.textContent = buildPreviewInitials();
        preview.appendChild(placeholder);
    };

    const renderPreviewImage = (src) => {
        if (!preview) return;
        preview.innerHTML = '';
        const img = document.createElement('img');
        img.src = src;
        img.alt = 'Foto profil';
        img.style.width = '100%';
        img.style.height = '100%';
        img.style.objectFit = 'cover';
        preview.appendChild(img);
    };

    fileInput?.addEventListener('change', (e) => {
        const file = e.target.files?.[0];
        if (!file) {
            return;
        }
        if (fileNameLabel) fileNameLabel.textContent = file.name;
        const reader = new FileReader();
        reader.onload = (ev) => {
            const imageSrc = ev.target?.result || '';
            renderPreviewImage(imageSrc);
            if (removePhotoField) removePhotoField.value = '0';
        };
        reader.readAsDataURL(file);
    });

    removePhotoBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        if (fileInput) fileInput.value = '';
        if (fileNameLabel) fileNameLabel.textContent = defaultFileLabel;
        renderPreviewPlaceholder();
        if (removePhotoField) removePhotoField.value = '1';
    });

    displayNameInput?.addEventListener('input', () => {
        if (preview?.querySelector('img')) {
            return;
        }

        renderPreviewPlaceholder();
    });
</script>
@endpush
