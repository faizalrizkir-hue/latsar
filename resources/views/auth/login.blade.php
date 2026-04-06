<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Dashboard Monitoring</title>
    <link rel="stylesheet" href="/css/login.css">
    @php
        $recaptchaSiteKey = config('services.recaptcha.site_key');
        $recaptchaAction = config('services.recaptcha.action', 'login');
        $recaptchaSkipOnLocal = app()->environment('local') && (bool) config('services.recaptcha.skip_on_local', true);
        $recaptchaEnabled = filled($recaptchaSiteKey) && filled(config('services.recaptcha.secret_key')) && !$recaptchaSkipOnLocal;
    @endphp
    @if ($recaptchaEnabled)
        <script src="https://www.google.com/recaptcha/api.js?render={{ $recaptchaSiteKey }}"></script>
    @endif
</head>
<body>
    <div class="loading-overlay" id="loadingOverlay" aria-live="polite" aria-label="Memproses login">
        <div class="loading-bubble" role="status">
            <div class="loading-content" id="loadingContent" aria-hidden="false">
                <div class="loading-icon" aria-hidden="true"><div class="spinner"></div></div>
                <div class="loading-text">
                    <strong>Memverifikasi akun</strong>
                    <span>Mohon tunggu sebentar...</span>
                    <div class="loading-dots" aria-hidden="true"><span></span><span></span><span></span></div>
                </div>
            </div>
            <div class="success-content" id="successContent" aria-hidden="true">
                <div class="success-icon">
                    <svg viewBox="0 0 52 52">
                        <circle class="check-circle" cx="26" cy="26" r="22"></circle>
                        <path class="check-path" d="M14 27l7 7 17-18"></path>
                    </svg>
                </div>
                <div class="success-text">
                    <strong>Login berhasil</strong>
                    <span>Mengalihkan ke dashboard...</span>
                </div>
            </div>
            <div class="error-content" id="errorContent" aria-hidden="true">
                <div class="error-icon">
                    <svg viewBox="0 0 52 52">
                        <circle class="error-circle" cx="26" cy="26" r="22"></circle>
                        <path class="error-line-one" d="M18 18l16 16"></path>
                        <path class="error-line-two" d="M34 18 18 34"></path>
                    </svg>
                </div>
                <div class="error-text">
                    <strong>Login gagal</strong>
                    <span id="errorMessage">Periksa kembali username dan kata sandi Anda.</span>
                </div>
            </div>
        </div>
    </div>
    @if (session('logout'))
        <div class="login-toast login-toast-success" id="logoutToast" role="status" aria-live="polite" aria-hidden="true">
            <div class="login-toast__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path d="M20 7 10.8 16.2 6 11.4"/>
                </svg>
            </div>
            <div class="login-toast__content">
                <strong>Logout berhasil</strong>
                <span>{{ session('logout') }}</span>
            </div>
            <button type="button" class="login-toast__close" id="logoutToastClose" aria-label="Tutup notifikasi logout">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <div class="layout">
        <div class="top-bar">
            <button type="button" class="theme-toggle" id="themeToggle" aria-pressed="true">
                <span class="icon" aria-hidden="true">
                    <svg class="icon-sun" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4.5"/><path d="M12 2.5v2.5M12 19v2.5M4.5 12H2M22 12h-2.5M5.6 5.6 4 4M20 20l-1.6-1.6M18.4 5.6 20 4M4 20l1.6-1.6"/></svg>
                    <svg class="icon-moon" viewBox="0 0 24 24"><path d="M20.5 14.5A8.5 8.5 0 0 1 9.5 3.5 8.5 8.5 0 1 0 20.5 14.5Z"/></svg>
                </span>
                <span class="text">Gelap</span>
            </button>
        </div>
        <section class="card">
            <div class="card-header">
                <div class="logo">
                    <img class="logo-light" src="/static/logo-sikap-light.png" alt="Logo SIKAP">
                    <img class="logo-dark" src="/static/logo-sikap-dark.png" alt="Logo SIKAP">
                </div>
                <p class="eyebrow" style="margin-bottom: 4px;">Self Assessment Kapabilitas APIP</p>
                <h1>Login Akun</h1>
                <p>Silakan masuk untuk melanjutkan ke Halaman Dashboard</p>
            </div>
            @if ($errors->any())
                <div class="alert" role="alert">{{ $errors->first() }}</div>
            @endif
            <form id="demo-form" method="POST" action="{{ route('login.perform') }}" novalidate>
                @csrf
                <input type="hidden" name="g-recaptcha-response" id="recaptchaToken">
                <div class="field">
                    <label for="username">NRK</label>
                    <input type="text" id="username" name="username" placeholder="Masukkan NRK" value="{{ old('username') }}" required autofocus aria-describedby="usernameError">
                    <div class="field-error" id="usernameError" aria-live="polite"></div>
                </div>
                <div class="field">
                    <label for="password">Kata sandi</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" required aria-describedby="passwordError">
                        <button type="button" class="toggle-password" id="passwordToggle" aria-label="Lihat password" aria-pressed="false">
                            <svg class="icon-eye" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/>
                                <circle cx="12" cy="12" r="2.8"/>
                            </svg>
                            <svg class="icon-eye-off" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M3 3l18 18"/>
                                <path d="M6.2 6.2 4 8.6S7.5 14.6 12 14.6c1.1 0 2.1-.2 3-.6"/>
                                <path d="M17.8 17.8 20 15.4S16.5 9.4 12 9.4c-.3 0-.7 0-1 .1"/>
                                <circle cx="12" cy="12" r="2.8"/>
                            </svg>
                        </button>
                    </div>
                    <div class="field-error" id="passwordError" aria-live="polite"></div>
                </div>
                <div class="actions">
                    <label class="remember">
                        <input type="checkbox" name="remember">
                        <span>Ingat saya</span>
                    </label>
                    <span class="forgot-password">
                        Lupa kata sandi?
                        <a href="https://wa.me/6282214004114" class="forgot-link" target="_blank" rel="noopener noreferrer">Hubungi admin</a>
                    </span>
                </div>
                <button
                    type="submit"
                    class="btn-primary g-recaptcha"
                    @if ($recaptchaEnabled)
                        data-sitekey="{{ $recaptchaSiteKey }}"
                        data-callback="onSubmit"
                        data-action="{{ $recaptchaAction }}"
                    @endif
                >
                    Masuk
                </button>
            </form>
            <div class="quick-links" aria-label="Quick Links aplikasi terkait">
                <div class="quick-links-separator"><span>Quick Gateway</span></div>
                <div class="quick-links-grid">
                    <div class="quick-link-item">
                        <a href="https://simantab.jakarta.go.id/" class="quick-link-anchor" target="_blank" rel="noopener noreferrer" title="SIMANTAB" aria-label="Buka SIMANTAB">
                            <span class="quick-link-logo">
                                <img src="/static/simantab-logo.png" alt="Logo SIMANTAB" loading="lazy">
                            </span>
                        </a>
                        <span class="quick-link-name">SIMANTAB</span>
                    </div>
                    <div class="quick-link-item">
                        <a href="https://siperisai.jakarta.go.id/" class="quick-link-anchor" target="_blank" rel="noopener noreferrer" title="SIPERISAI" aria-label="Buka SIPERISAI">
                            <span class="quick-link-logo">
                                <img src="/static/siperisai-logo.png" alt="Logo SIPERISAI" loading="lazy">
                            </span>
                        </a>
                        <span class="quick-link-name">SIPERISAI</span>
                    </div>
                    <div class="quick-link-item">
                        <a href="https://ams.jakarta.go.id/" class="quick-link-anchor" target="_blank" rel="noopener noreferrer" title="AMS" aria-label="Buka AMS">
                            <span class="quick-link-logo">
                                <img src="/static/ams-logo.png" alt="Logo AMS" loading="lazy">
                            </span>
                        </a>
                        <span class="quick-link-name">AMS</span>
                    </div>
                    <div class="quick-link-item">
                        <a href="https://eklinik.jakarta.go.id/" class="quick-link-anchor" target="_blank" rel="noopener noreferrer" title="E-Klinik" aria-label="Buka E-Klinik">
                            <span class="quick-link-logo">
                                <img src="/static/eklinik-logo.png" alt="Logo E-Klinik" loading="lazy">
                            </span>
                        </a>
                        <span class="quick-link-name">E-Klinik</span>
                    </div>
                    <div class="quick-link-item">
                        <a href="https://sipadu.jakarta.go.id/" class="quick-link-anchor" target="_blank" rel="noopener noreferrer" title="SiPADU" aria-label="Buka SiPADU">
                            <span class="quick-link-logo">
                                <img src="/static/sipadu-logo.png" alt="Logo SiPADU" loading="lazy">
                            </span>
                        </a>
                        <span class="quick-link-name">SiPADU</span>
                    </div>
                    <div class="quick-link-item">
                        <a href="https://inspektorat.jakarta.go.id/sepakat/login" class="quick-link-anchor" target="_blank" rel="noopener noreferrer" title="SEPAKAT" aria-label="Buka SEPAKAT">
                            <span class="quick-link-logo">
                                <img src="/static/sepakat-logo.png" alt="Logo SEPAKAT" loading="lazy">
                            </span>
                        </a>
                        <span class="quick-link-name">SEPAKAT</span>
                    </div>
                    <div class="quick-link-item">
                        <a href="https://cacm.jakarta.go.id/" class="quick-link-anchor" target="_blank" rel="noopener noreferrer" title="CACM" aria-label="Buka CACM">
                            <span class="quick-link-logo">
                                <img src="/static/cacm-logo.png" alt="Logo CACM" loading="lazy">
                            </span>
                        </a>
                        <span class="quick-link-name">CACM</span>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="watermark">(c) Inspektorat Provinsi DKI Jakarta 2025</div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const root = document.documentElement;
            const toggleBtn = document.getElementById('themeToggle');
            const label = toggleBtn.querySelector('.text');
            const STORAGE_KEY = 'dashboard-theme';
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            const usernameError = document.getElementById('usernameError');
            const passwordError = document.getElementById('passwordError');
            const passwordToggle = document.getElementById('passwordToggle');
            const form = document.querySelector('form');
            const overlay = document.getElementById('loadingOverlay');
            const loadingContent = document.getElementById('loadingContent');
            const successContent = document.getElementById('successContent');
            const errorContent = document.getElementById('errorContent');
            const errorMessage = document.getElementById('errorMessage');
            const logoutToast = document.getElementById('logoutToast');
            const logoutToastClose = document.getElementById('logoutToastClose');
            const submitBtn = form.querySelector('button[type="submit"]');
            const recaptchaTokenInput = document.getElementById('recaptchaToken');
            const recaptchaSiteKey = @json($recaptchaEnabled ? $recaptchaSiteKey : null);
            const recaptchaAction = @json($recaptchaAction);
            const isLocalEnv = @json(app()->environment('local'));
            let overlayTimer = null;
            let redirectTimer = null;
            let logoutToastTimer = null;

            const initialTheme = (() => {
                const stored = localStorage.getItem(STORAGE_KEY);
                if (stored === 'light' || stored === 'dark') return stored;
                return 'light';
            })();

            function applyTheme(theme, persist = true) {
                root.setAttribute('data-theme', theme);
                toggleBtn.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
                label.textContent = theme === 'dark' ? 'Gelap' : 'Terang';
                if (persist) localStorage.setItem(STORAGE_KEY, theme);
            }
            applyTheme(initialTheme, false);
            toggleBtn.addEventListener('click', () => {
                const nextTheme = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                applyTheme(nextTheme);
            });

            passwordToggle.addEventListener('click', () => {
                const isHidden = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isHidden ? 'text' : 'password');
                passwordToggle.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
                passwordToggle.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Lihat password');
            });

            function clearAlert() {
                const alertEl = form.parentElement.querySelector('.alert');
                if (!alertEl) return;
                alertEl.classList.add('hidden');
            }

            function setFieldError(inputEl, errorEl, message) {
                if (inputEl) {
                    inputEl.classList.add('is-invalid');
                    inputEl.setAttribute('aria-invalid', 'true');
                }
                if (!errorEl) return;
                errorEl.textContent = message;
                errorEl.classList.add('show');
            }

            function clearFieldError(inputEl, errorEl) {
                if (inputEl) {
                    inputEl.classList.remove('is-invalid');
                    inputEl.removeAttribute('aria-invalid');
                }
                if (!errorEl) return;
                errorEl.textContent = '';
                errorEl.classList.remove('show');
            }

            function clearFieldErrors() {
                clearFieldError(usernameInput, usernameError);
                clearFieldError(passwordInput, passwordError);
            }

            function validateFormInputs() {
                clearAlert();
                clearFieldErrors();
                let firstInvalid = null;

                const usernameValue = (usernameInput?.value || '').trim();
                const passwordValue = passwordInput?.value || '';

                if (!usernameValue) {
                    setFieldError(usernameInput, usernameError, 'Username wajib diisi.');
                    firstInvalid = firstInvalid || usernameInput;
                }

                if (!passwordValue) {
                    setFieldError(passwordInput, passwordError, 'Kata sandi wajib diisi.');
                    firstInvalid = firstInvalid || passwordInput;
                } else if (passwordValue.length < 6) {
                    setFieldError(passwordInput, passwordError, 'Kata sandi minimal 6 karakter.');
                    firstInvalid = firstInvalid || passwordInput;
                }

                if (firstInvalid) {
                    firstInvalid.focus();
                    return false;
                }

                return true;
            }

            usernameInput?.addEventListener('input', () => {
                if ((usernameInput.value || '').trim() !== '') {
                    clearFieldError(usernameInput, usernameError);
                }
            });

            passwordInput?.addEventListener('input', () => {
                if ((passwordInput.value || '').length >= 6) {
                    clearFieldError(passwordInput, passwordError);
                }
            });

            function clearOverlayTimer() {
                if (!overlayTimer) return;
                clearTimeout(overlayTimer);
                overlayTimer = null;
            }

            function clearRedirectTimer() {
                if (!redirectTimer) return;
                clearTimeout(redirectTimer);
                redirectTimer = null;
            }

            function clearLogoutToastTimer() {
                if (!logoutToastTimer) return;
                clearTimeout(logoutToastTimer);
                logoutToastTimer = null;
            }

            function hideLogoutToast() {
                if (!logoutToast) return;
                clearLogoutToastTimer();
                logoutToast.classList.remove('show');
                logoutToast.setAttribute('aria-hidden', 'true');
            }

            function showLogoutToast() {
                if (!logoutToast) return;
                clearLogoutToastTimer();
                requestAnimationFrame(() => {
                    logoutToast.classList.add('show');
                    logoutToast.setAttribute('aria-hidden', 'false');
                });
                logoutToastTimer = setTimeout(() => {
                    hideLogoutToast();
                }, 3200);
            }

            function resetOverlayState() {
                clearOverlayTimer();
                overlay.classList.remove('is-error');
                overlay.setAttribute('aria-label', 'Memproses login');
                loadingContent.style.display = 'flex';
                loadingContent.setAttribute('aria-hidden', 'false');
                successContent.style.display = 'none';
                successContent.setAttribute('aria-hidden', 'true');
                errorContent.style.display = 'none';
                errorContent.setAttribute('aria-hidden', 'true');
            }

            function renderAlert(message) {
                let alertEl = form.parentElement.querySelector('.alert');
                if (!alertEl) {
                    alertEl = document.createElement('div');
                    alertEl.className = 'alert';
                    form.parentElement.insertBefore(alertEl, form);
                }
                alertEl.textContent = message;
                alertEl.classList.remove('hidden');
            }

            function showErrorState(message) {
                overlay.classList.add('is-error');
                overlay.setAttribute('aria-label', 'Login gagal. Periksa kembali kredensial Anda.');
                loadingContent.style.display = 'none';
                loadingContent.setAttribute('aria-hidden', 'true');
                successContent.style.display = 'none';
                successContent.setAttribute('aria-hidden', 'true');
                errorMessage.textContent = message;
                errorContent.style.display = 'flex';
                errorContent.setAttribute('aria-hidden', 'false');

                overlayTimer = setTimeout(() => {
                    overlay.classList.remove('show');
                    resetOverlayState();
                    submitBtn.disabled = false;
                }, 1500);
            }

            function showSuccessState(redirectUrl) {
                overlay.classList.remove('is-error');
                overlay.setAttribute('aria-label', 'Login berhasil, mengalihkan...');
                loadingContent.style.display = 'none';
                loadingContent.setAttribute('aria-hidden', 'true');
                errorContent.style.display = 'none';
                errorContent.setAttribute('aria-hidden', 'true');
                successContent.style.display = 'flex';
                successContent.setAttribute('aria-hidden', 'false');

                redirectTimer = setTimeout(() => {
                    window.location.href = redirectUrl || '/';
                }, 900);
            }

            function executeLoginRequest(recaptchaToken = null) {
                const fd = new FormData(form);
                if (recaptchaToken) {
                    fd.set('g-recaptcha-response', recaptchaToken);
                }

                const csrfToken = String(fd.get('_token') || '').trim();
                const requestHeaders = {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                };
                if (csrfToken !== '') {
                    requestHeaders['X-CSRF-TOKEN'] = csrfToken;
                }

                return fetch(form.action, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: requestHeaders,
                    body: fd
                })
                .then(async (res) => {
                    let data = null;
                    const contentType = String(res.headers.get('content-type') || '').toLowerCase();
                    if (contentType.includes('application/json')) {
                        data = await res.json();
                    }

                    return { ok: res.ok, status: res.status, data };
                })
                .then(({ ok, status, data }) => {
                    if (status === 419) {
                        const message = 'Sesi keamanan telah kedaluwarsa. Halaman akan dimuat ulang.';
                        renderAlert(message);
                        showErrorState(message);
                        setTimeout(() => window.location.reload(), 1000);
                        return;
                    }

                    if (ok && data?.ok) {
                        showSuccessState(data.redirect);
                    } else {
                        const message = data?.message || 'Login gagal. Silakan coba lagi.';
                        renderAlert(message);
                        showErrorState(message);
                    }
                })
                .catch(() => {
                    const message = 'Koneksi gagal. Silakan coba lagi.';
                    renderAlert(message);
                    showErrorState(message);
                });
            }

            function obtainRecaptchaToken() {
                if (!recaptchaSiteKey) {
                    return Promise.resolve(null);
                }

                if (!window.grecaptcha || typeof window.grecaptcha.ready !== 'function' || typeof window.grecaptcha.execute !== 'function') {
                    return Promise.reject(new Error('reCAPTCHA API belum siap.'));
                }

                return new Promise((resolve, reject) => {
                    window.grecaptcha.ready(() => {
                        window.grecaptcha.execute(recaptchaSiteKey, { action: recaptchaAction || 'login' })
                            .then(resolve)
                            .catch(reject);
                    });
                });
            }

            window.onSubmit = function onSubmit(token) {
                if (recaptchaTokenInput && token) {
                    recaptchaTokenInput.value = token;
                }
                return token;
            };

            form.addEventListener('submit', (e) => {
                e.preventDefault();
                clearRedirectTimer();
                if (!validateFormInputs()) {
                    submitBtn.disabled = false;
                    return;
                }
                resetOverlayState();
                overlay.classList.add('show');
                submitBtn.disabled = true;

                obtainRecaptchaToken()
                .then((token) => {
                    if (token) {
                        window.onSubmit(token);
                    }
                    return executeLoginRequest(token);
                })
                .catch((error) => {
                    let message = 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.';
                    if (isLocalEnv && error?.message) {
                        message += ` (${error.message})`;
                    }
                    renderAlert(message);
                    showErrorState(message);
                });
            });

            if (logoutToast) {
                showLogoutToast();
                logoutToastClose?.addEventListener('click', hideLogoutToast);
            }
        });
    </script>
</body>
</html>
