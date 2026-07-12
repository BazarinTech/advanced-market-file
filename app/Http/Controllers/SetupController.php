<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SetupController extends Controller
{
    public function index()
    {
        return view('setup.index', ['checks' => $this->requirements()]);
    }

    public function testDb(Request $request)
    {
        $request->validate([
            'db_host'     => 'required|string',
            'db_port'     => 'required|integer',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        try {
            new \PDO(
                sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                    $request->db_host, $request->db_port, $request->db_database),
                $request->db_username,
                $request->db_password ?? '',
                [\PDO::ATTR_TIMEOUT => 5, \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
            return response()->json(['success' => true, 'message' => 'Connection successful!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function install(Request $request)
    {
        $data = $request->validate([
            'db_host'            => 'required|string',
            'db_port'            => 'required|integer',
            'db_database'        => 'required|string',
            'db_username'        => 'required|string',
            'db_password'        => 'nullable|string',
            'app_name'           => 'required|string|max:60',
            'app_url'            => 'required|string',
            'app_env'            => 'required|in:production,local',
            'app_download_url'   => 'nullable|string',
            'whatsapp_group_url' => 'nullable|string',
            'palpluss_auth'      => 'required|string',
            'palpluss_channel_id'=> 'required|string',
            'palpluss_base_url'  => 'nullable|string',
            'support_url'        => 'nullable|string',
            'support_email'      => 'nullable|email',
            'support_phone'      => 'nullable|string',
            'support_network'    => 'nullable|string',
            'admin_email'        => 'required|email',
            'admin_phone'        => 'required|string',
            'admin_password'     => 'required|min:8',
        ]);

        try {
            // 1. Write .env
            $this->writeEnv($data);

            // 2. Switch DB config to new credentials in current process
            $this->reconfigDb($data);

            // 3. Clear config cache so artisan picks up new .env
            Artisan::call('config:clear');

            // 4. Generate app key (writes into .env)
            Artisan::call('key:generate', ['--force' => true]);

            // 5. Run all migrations
            Artisan::call('migrate', ['--force' => true]);

            // 6. Create admin user + earnings row
            $this->createAdmin($data);

            // 7. Seed default settings if empty
            $this->seedSettings();

            // 8. Write lock file
            file_put_contents(storage_path('installed.lock'), now()->toDateTimeString());

            return response()->json(['success' => true, 'redirect' => url('/login')]);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ── private helpers ──────────────────────────────────────────────────────

    private function reconfigDb(array $d): void
    {
        config([
            'database.connections.mysql.host'     => $d['db_host'],
            'database.connections.mysql.port'     => (int) $d['db_port'],
            'database.connections.mysql.database' => $d['db_database'],
            'database.connections.mysql.username' => $d['db_username'],
            'database.connections.mysql.password' => $d['db_password'] ?? '',
        ]);
        DB::purge('mysql');
        DB::reconnect('mysql');
    }

    private function writeEnv(array $d): void
    {
        $appUrl      = rtrim($d['app_url'], '/');
        $appName     = $d['app_name'];
        $env         = $d['app_env'];
        $debug       = $env === 'local' ? 'true' : 'false';
        $dbPass      = $d['db_password'] ?? '';
        $palBase     = $d['palpluss_base_url'] ?? 'https://api.palpluss.com/v1';
        $dlUrl       = $d['app_download_url']   ?? '';
        $waUrl       = $d['whatsapp_group_url'] ?? '';
        $suppUrl     = $d['support_url']         ?? '';
        $suppEmail   = $d['support_email']        ?? '';
        $suppPhone   = $d['support_phone']        ?? '';
        $suppNetwork = $d['support_network']      ?? 'Safaricom';

        $content = <<<ENV
APP_NAME="{$appName}"
APP_ENV={$env}
APP_KEY=
APP_DEBUG={$debug}
APP_URL={$appUrl}
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US
APP_MAINTENANCE_DRIVER=file
BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST={$d['db_host']}
DB_PORT={$d['db_port']}
DB_DATABASE={$d['db_database']}
DB_USERNAME={$d['db_username']}
DB_PASSWORD={$dbPass}

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="\${APP_NAME}"

PALPLUSS_BASE_URL={$palBase}
PALPLUSS_AUTH={$d['palpluss_auth']}
PALPLUSS_CHANNEL_ID={$d['palpluss_channel_id']}
PALPLUSS_CALLBACK_URL={$appUrl}/callback

APP_DOWNLOAD_URL={$dlUrl}
WHATSAPP_GROUP_URL={$waUrl}
CUSTOMER_SUPPORT_URL={$suppUrl}
SUPPORT_EMAIL={$suppEmail}
SUPPORT_PHONE={$suppPhone}
SUPPORT_NETWORK={$suppNetwork}
ENV;

        file_put_contents(base_path('.env'), $content);
    }

    private function createAdmin(array $d): void
    {
        // Skip if this email already exists (re-run protection)
        if (DB::table('users')->where('email', $d['admin_email'])->exists()) {
            return;
        }

        DB::table('users')->insert([
            'email'    => $d['admin_email'],
            'phone'    => $d['admin_phone'],
            'passwrd'  => $d['admin_password'],
            'password' => Hash::make($d['admin_password']),
            'role'     => 'admin',
            'status'   => 'Active',
            'refer'    => 0,
            'country'  => '254',
        ]);

        DB::table('earnings')->insert([
            'email'    => $d['admin_email'],
            'balance'  => 0,
            'deposit'  => 0,
            'withdraw' => 0,
            'referral' => 0,
            'bonus'    => 0,
            'totals'   => 0,
            'roll'     => 0,
        ]);
    }

    private function seedSettings(): void
    {
        if (DB::table('settings')->count() > 0) return;

        DB::table('settings')->insert([
            ['key' => 'withdrawal_fee', 'value' => '6'],
            ['key' => 'withdrawal_min', 'value' => '200'],
        ]);
    }

    private function requirements(): array
    {
        $phpVersion = PHP_VERSION;
        $phpOk      = version_compare($phpVersion, '8.2.0', '>=');

        $extensions = [
            'pdo', 'pdo_mysql', 'mbstring', 'openssl',
            'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo', 'curl',
        ];
        $extChecks = array_combine($extensions, array_map('extension_loaded', $extensions));

        $permissions = [
            'storage/'         => is_writable(storage_path()),
            'bootstrap/cache/' => is_writable(base_path('bootstrap/cache')),
        ];

        $allPass = $phpOk
            && !in_array(false, $extChecks, true)
            && !in_array(false, $permissions, true);

        return compact('phpVersion', 'phpOk', 'extChecks', 'permissions', 'allPass');
    }
}
