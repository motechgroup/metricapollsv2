<?php

use Illuminate\Support\Facades\Route;

// Web Migration Script Route for Shared Hosting without SSH Terminal Access
Route::get('/run-system-migrations', function () {
    try {
        $output = [];

        // 1. Force database migrations
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $output[] = "1. MIGRATIONS LOG:\n" . \Illuminate\Support\Facades\Artisan::output();

        // 2. Force seeders (Regions, Counties, Constituencies, Political Parties, Politicians)
        if (class_exists('GeographicAndPoliticalSeeder')) {
            \Illuminate\Support\Facades\Artisan::call('db:seed', [
                '--class' => 'GeographicAndPoliticalSeeder',
                '--force' => true,
            ]);
            $output[] = "2. SEEDERS LOG:\n" . \Illuminate\Support\Facades\Artisan::output();
        }

        // 3. Clear application caches
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        $output[] = "3. SYSTEM CACHES CLEARED SUCCESSFULLY!";

        $fullLog = implode("\n----------------------------------------\n", $output);

        return response("
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <title>Database Migrations Executed - Metrica Polls</title>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body { font-family: system-ui, -apple-system, sans-serif; background: #0f172a; color: #f8fafc; padding: 40px 20px; margin: 0; line-height: 1.6; }
                .container { max-width: 800px; margin: 0 auto; background: #1e293b; border-radius: 20px; padding: 36px; border: 1px solid #334155; box-shadow: 0 15px 35px rgba(0,0,0,0.5); }
                h1 { color: #4ade80; font-size: 24px; font-weight: 800; margin-top: 0; display: flex; align-items: center; gap: 10px; }
                p { color: #cbd5e1; font-size: 14px; }
                pre { background: #090d16; padding: 20px; border-radius: 14px; color: #38bdf8; font-size: 13px; overflow-x: auto; border: 1px solid #1e293b; white-space: pre-wrap; word-break: break-all; font-family: monospace; }
                .btn { display: inline-block; background: #2563eb; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 12px; font-weight: 800; font-size: 14px; margin-top: 20px; transition: background 150ms ease; }
                .btn:hover { background: #1d4ed8; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h1>✅ Database Migrations & Seeders Completed!</h1>
                <p>The database schema and geographic/political seeders have been updated on the server.</p>
                <pre>" . e($fullLog) . "</pre>
                <a href='/public-opinion' class='btn'>← Return to Public Opinion Polls</a>
            </div>
        </body>
        </html>
        ", 200, ['Content-Type' => 'text/html']);
    } catch (\Throwable $e) {
        return response("
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <title>Migration Exception - Metrica Polls</title>
            <style>
                body { font-family: system-ui, -apple-system, sans-serif; background: #0f172a; color: #f8fafc; padding: 40px 20px; margin: 0; }
                .container { max-width: 800px; margin: 0 auto; background: #1e293b; border-radius: 20px; padding: 36px; border: 1px solid #ef4444; }
                h1 { color: #f87171; font-size: 24px; margin-top: 0; }
                pre { background: #090d16; padding: 20px; border-radius: 14px; color: #fca5a5; font-size: 13px; overflow-x: auto; font-family: monospace; white-space: pre-wrap; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h1>⚠️ Migration Exception Occurred</h1>
                <pre>" . e($e->getMessage() . "\n\n" . $e->getTraceAsString()) . "</pre>
            </div>
        </body>
        </html>
        ", 500, ['Content-Type' => 'text/html']);
    }
});

Route::get('/run-migrations', function () {
    return redirect('/run-system-migrations');
});
