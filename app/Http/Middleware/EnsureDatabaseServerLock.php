<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureDatabaseServerLock
{
    public function handle(Request $request, Closure $next): Response
    {
        $defaultConnection = (string) config('database.default', 'mysql');
        $connectionConfig = (array) config('database.connections.'.$defaultConnection, []);

        $expectedUuid = strtolower(trim((string) config('database.server_lock.uuid', '')));
        $expectedServerId = trim((string) config('database.server_lock.server_id', ''));
        $expectedDatabase = strtolower(trim((string) config('database.server_lock.database', '')));
        if ($expectedDatabase === '') {
            $expectedDatabase = strtolower(trim((string) ($connectionConfig['database'] ?? '')));
        }

        if ($expectedUuid === '' && $expectedServerId === '' && $expectedDatabase === '') {
            return $next($request);
        }

        try {
            $currentDb = trim((string) DB::scalar('SELECT DATABASE()'));
        } catch (\Throwable $exception) {
            Log::error('DB lock check failed.', [
                'message' => $exception->getMessage(),
            ]);

            abort(503, 'Koneksi database gagal diverifikasi. Silakan cek koneksi DB Laragon.');
        }

        if ($expectedDatabase !== '') {
            $currentDbNormalized = strtolower($currentDb);
            if ($currentDbNormalized === '' || !hash_equals($expectedDatabase, $currentDbNormalized)) {
                Log::critical('Database name mismatch detected.', [
                    'expected_database' => $expectedDatabase,
                    'current_database' => $currentDbNormalized,
                    'host' => (string) ($connectionConfig['host'] ?? ''),
                    'port' => (string) ($connectionConfig['port'] ?? ''),
                    'connection' => $defaultConnection,
                ]);

                abort(503, 'Nama database tidak sesuai lock Laragon. Akses dihentikan untuk mencegah data tertukar.');
            }
        }

        if ($expectedUuid !== '') {
            $currentUuid = '';
            $uuidSupported = true;

            try {
                $currentUuid = strtolower(trim((string) DB::scalar('SELECT @@server_uuid')));
            } catch (\Throwable $exception) {
                $uuidSupported = false;
                $message = strtolower($exception->getMessage());
                $isUnsupportedUuidVariable = str_contains($message, 'unknown system variable')
                    && str_contains($message, 'server_uuid');

                if (!$isUnsupportedUuidVariable) {
                    Log::error('DB lock UUID check failed.', [
                        'message' => $exception->getMessage(),
                    ]);

                    abort(503, 'Koneksi database gagal diverifikasi. Silakan cek koneksi DB Laragon.');
                }
            }

            if ($uuidSupported) {
                if ($currentUuid === '' || !hash_equals($expectedUuid, $currentUuid)) {
                    Log::critical('Database server UUID mismatch detected.', [
                        'expected_uuid' => $expectedUuid,
                        'current_uuid' => $currentUuid,
                        'current_db' => $currentDb,
                        'host' => (string) ($connectionConfig['host'] ?? ''),
                        'port' => (string) ($connectionConfig['port'] ?? ''),
                        'connection' => $defaultConnection,
                    ]);

                    abort(503, 'Database tidak sesuai server Laragon yang dikunci. Akses dihentikan untuk mencegah data tertukar.');
                }
            } else {
                Log::critical('Database engine mismatch detected while UUID lock is enabled (server_uuid not supported).', [
                    'current_db' => $currentDb,
                    'host' => (string) ($connectionConfig['host'] ?? ''),
                    'port' => (string) ($connectionConfig['port'] ?? ''),
                    'connection' => $defaultConnection,
                ]);
                abort(503, 'Database yang terhubung tidak sesuai lock UUID Laragon. Pastikan service DB Laragon aktif dan koneksi mengarah ke server yang benar.');
            }
        }

        if ($expectedServerId !== '') {
            try {
                $currentServerId = trim((string) DB::scalar('SELECT @@server_id'));
            } catch (\Throwable $exception) {
                Log::error('DB lock server_id check failed.', [
                    'message' => $exception->getMessage(),
                ]);

                abort(503, 'Koneksi database gagal diverifikasi. Silakan cek koneksi DB Laragon.');
            }

            if ($currentServerId === '' || !hash_equals($expectedServerId, $currentServerId)) {
                Log::critical('Database server_id mismatch detected.', [
                    'expected_server_id' => $expectedServerId,
                    'current_server_id' => $currentServerId,
                    'current_db' => $currentDb,
                    'host' => (string) ($connectionConfig['host'] ?? ''),
                    'port' => (string) ($connectionConfig['port'] ?? ''),
                    'connection' => $defaultConnection,
                ]);

                abort(503, 'Database tidak sesuai server Laragon yang dikunci. Akses dihentikan untuk mencegah data tertukar.');
            }
        }

        return $next($request);
    }
}
