<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Client\Response as HttpClientResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    private ?string $lastRecaptchaError = null;

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $recaptchaSecret = (string) config('services.recaptcha.secret_key');
        $recaptchaSkipOnLocal = app()->environment('local') && (bool) config('services.recaptcha.skip_on_local', true);
        if ($recaptchaSecret !== '' && !$recaptchaSkipOnLocal) {
            $recaptchaToken = trim((string) $request->input('g-recaptcha-response', ''));
            if ($recaptchaToken === '') {
                return $this->buildLoginErrorResponse($request, 'Verifikasi reCAPTCHA wajib diisi.');
            }

            if (!$this->verifyRecaptchaToken($recaptchaToken, $request->ip())) {
                Log::warning('Login blocked by reCAPTCHA verification', [
                    'ip' => $request->ip(),
                    'username' => (string) $request->input('username', ''),
                    'reason' => $this->lastRecaptchaError,
                ]);

                $message = 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.';
                if (app()->environment('local') && $this->lastRecaptchaError) {
                    $message .= ' ('.$this->lastRecaptchaError.')';
                }

                return $this->buildLoginErrorResponse($request, $message);
            }
        }

        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $user = Account::where('username', $credentials['username'])
            ->where('active', true)
            ->first();

        $isValid = $user && (Hash::check($credentials['password'], $user->password_hash) || hash_equals($user->password_hash, $credentials['password']));

        if (!$isValid) {
            return $this->buildLoginErrorResponse($request, 'Username atau password salah atau akun tidak aktif.');
        }

        Session::put('user', [
            'id' => $user->id,
            'username' => $user->username,
            'display_name' => $user->display_name,
            'role' => $user->role,
            'role_label' => Account::roleLabel($user->role),
            'profile_photo' => $user->profile_photo,
        ]);
        Session::put('last_activity_at', time());

        // simpan meta login terakhir
        $user->update([
            'last_login_ip' => $request->ip(),
            'last_login_device' => substr((string)$request->userAgent(), 0, 255),
        ]);

        $welcomeName = trim((string) ($user->display_name ?: $user->username));
        $welcomeRole = Account::roleLabel($user->role);
        session()->flash('login_welcome_toast', [
            'type' => 'success',
            'title' => 'Selamat datang, '.$welcomeName,
            'message' => $welcomeRole,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'redirect' => route('dashboard'),
                'username' => $user->username,
                'display_name' => $user->display_name,
                'role' => $user->role,
                'role_label' => Account::roleLabel($user->role),
            ]);
        }

        return redirect()->intended(route('dashboard'));
    }

    public function logout()
    {
        Session::forget(['user', 'last_activity_at']);
        Session::invalidate();
        Session::regenerateToken();

        return redirect()
            ->route('login.form')
            ->with('logout', 'Anda telah logout dari sistem.');
    }

    private function buildLoginErrorResponse(Request $request, string $message)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['ok' => false, 'message' => $message], 422);
        }

        return back()->withErrors(['login' => $message])->withInput();
    }

    private function verifyRecaptchaToken(string $token, ?string $ip): bool
    {
        $this->lastRecaptchaError = null;
        $secret = (string) config('services.recaptcha.secret_key');
        if ($secret === '' || $token === '') {
            $this->lastRecaptchaError = 'secret/token kosong';
            return false;
        }

        try {
            /** @var HttpClientResponse $response */
            $response = Http::asForm()
                ->timeout(10)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => $ip,
                ]);
        } catch (\Throwable $e) {
            $this->lastRecaptchaError = 'request ke Google gagal';
            return false;
        }

        if (!$response->ok()) {
            $this->lastRecaptchaError = 'status HTTP verifikasi '.$response->status();
            return false;
        }

        $payload = $response->json();
        if (!is_array($payload) || !($payload['success'] ?? false)) {
            $errorCodes = is_array($payload) && isset($payload['error-codes']) && is_array($payload['error-codes'])
                ? implode(',', $payload['error-codes'])
                : 'unknown';
            $this->lastRecaptchaError = 'error-codes: '.$errorCodes;
            return false;
        }

        $expectedAction = (string) config('services.recaptcha.action', 'login');
        $actualAction = (string) ($payload['action'] ?? '');
        if ($actualAction !== '' && $actualAction !== $expectedAction) {
            $this->lastRecaptchaError = 'action mismatch ('.$actualAction.' != '.$expectedAction.')';
            return false;
        }

        if (array_key_exists('score', $payload)) {
            $score = (float) $payload['score'];
            $minScore = (float) config('services.recaptcha.min_score', 0.5);
            if ($score < $minScore) {
                $this->lastRecaptchaError = 'score '.$score.' di bawah min '.$minScore;
                return false;
            }
        }

        return true;
    }
}
