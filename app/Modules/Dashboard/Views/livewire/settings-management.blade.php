@section('page_title', 'System Settings')

<div class="space-y-8 animate-fade-in">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">System Configurations</h1>
        <p class="text-sm text-gray-500">Manage branding details, SEO meta definitions, Google Analytics tracking scripts, and platform maintenance triggers.</p>
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-md">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left & Middle: Settings Cards -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Branding Card -->
                <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm space-y-6">
                    <h2 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">Branding & Logo Identity</h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="sm:col-span-2">
                            <label for="site_title" class="block text-sm font-medium text-gray-700">Platform Title</label>
                            <input wire:model="site_title" type="text" id="site_title" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                            @error('site_title') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Site Logo -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Site Logo</label>
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-32 border border-gray-200 rounded flex items-center justify-center bg-gray-50 overflow-hidden">
                                    @if ($site_logo)
                                        <img src="{{ $site_logo->temporaryUrl() }}" class="h-8 object-contain">
                                    @else
                                        <img src="{{ asset($logoPath) }}" class="h-8 object-contain" onerror="this.onerror=null; this.src='/favicon.png';">
                                    @endif
                                </div>
                                <input wire:model="site_logo" type="file" id="site_logo" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xxs file:font-semibold file:bg-gray-900 file:text-white hover:file:bg-gray-800">
                            </div>
                            @error('site_logo') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Favicon -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Favicon</label>
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 border border-gray-200 rounded flex items-center justify-center bg-gray-50 overflow-hidden">
                                    @if ($site_favicon)
                                        <img src="{{ $site_favicon->temporaryUrl() }}" class="h-6 w-6 object-contain">
                                    @else
                                        <img src="{{ asset($faviconPath) }}" class="h-6 w-6 object-contain" onerror="this.onerror=null; this.src='/favicon.png';">
                                    @endif
                                </div>
                                <input wire:model="site_favicon" type="file" id="site_favicon" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xxs file:font-semibold file:bg-gray-900 file:text-white hover:file:bg-gray-800">
                            </div>
                            @error('site_favicon') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="site_description" class="block text-sm font-medium text-gray-700">Meta Site Description</label>
                            <textarea wire:model="site_description" id="site_description" rows="3" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"></textarea>
                            @error('site_description') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="site_footer" class="block text-sm font-medium text-gray-700">Footer Attribution Text</label>
                            <input wire:model="site_footer" type="text" id="site_footer" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                            @error('site_footer') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- SEO, Theme & Scripts -->
                <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm space-y-6">
                    <h2 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">SEO, Theme & Marketing Integrations</h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="site_theme" class="block text-sm font-medium text-gray-700">UI Application Theme</label>
                            <select wire:model="site_theme" id="site_theme" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                                <option value="Light">Light Mode (Minimalist)</option>
                                <option value="Dark">Dark Mode (Premium)</option>
                                <option value="Glassmorphism">Glassmorphism (Modern)</option>
                            </select>
                            @error('site_theme') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">System Mode</label>
                            <div class="mt-3 flex items-center">
                                <input wire:model="maintenance_mode" type="checkbox" id="maintenance_mode" class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                                <label for="maintenance_mode" class="ml-2 text-sm text-gray-700 font-bold text-yellow-800">Activate System Maintenance Mode</label>
                            </div>
                            @error('maintenance_mode') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="site_seo_keywords" class="block text-sm font-medium text-gray-700">SEO Keywords (Comma separated)</label>
                            <input wire:model="site_seo_keywords" type="text" id="site_seo_keywords" placeholder="market research, public opinion, parastatal audits" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                            @error('site_seo_keywords') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="analytics_code" class="block text-sm font-medium text-gray-700">Analytics Tracking Code (Google Tag Manager, scripts)</label>
                            <textarea wire:model="analytics_code" id="analytics_code" rows="3" placeholder="<!-- Google Analytics code -->" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-xs font-mono focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"></textarea>
                            @error('analytics_code') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Original Configs Card (Compatibility) -->
                <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm space-y-6">
                    <h2 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">Security & Support Configurations</h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="support_email" class="block text-sm font-medium text-gray-700">Support Email Address</label>
                            <input wire:model="support_email" type="email" id="support_email" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                            @error('support_email') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="otp_expiry" class="block text-sm font-medium text-gray-700">OTP Expiry (Minutes)</label>
                            <input wire:model="otp_expiry" type="number" id="otp_expiry" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                            @error('otp_expiry') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="rate_limit_login" class="block text-sm font-medium text-gray-700">Login Rate Limit (Attempts / Minute)</label>
                            <input wire:model="rate_limit_login" type="number" id="rate_limit_login" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                            @error('rate_limit_login') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Audit Monitoring</label>
                            <div class="mt-3 flex items-center">
                                <input wire:model="enable_audit_logs" type="checkbox" id="enable_audit_logs" class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                                <label for="enable_audit_logs" class="ml-2 text-sm text-gray-700">Enable system audit logs tracking</label>
                            </div>
                            @error('enable_audit_logs') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- SMTP Server Configurations -->
                <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm space-y-6 animate-fade-in">
                    <h2 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">SMTP Mail Server Settings</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">SMTP Host</label>
                            <input wire:model="mail_host" type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white focus:border-gray-900 focus:outline-none">
                            @error('mail_host') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">SMTP Port</label>
                            <input wire:model="mail_port" type="number" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white focus:border-gray-900 focus:outline-none">
                            @error('mail_port') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">SMTP Username</label>
                            <input wire:model="mail_username" type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white focus:border-gray-900 focus:outline-none">
                            @error('mail_username') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">SMTP Password</label>
                            <input wire:model="mail_password" type="password" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white focus:border-gray-900 focus:outline-none">
                            @error('mail_password') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Encryption Protocol</label>
                            <select wire:model="mail_encryption" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white focus:border-gray-900 focus:outline-none">
                                <option value="tls">TLS</option>
                                <option value="ssl">SSL</option>
                                <option value="none">None</option>
                            </select>
                            @error('mail_encryption') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">From Name</label>
                            <input wire:model="mail_from_name" type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white focus:border-gray-900 focus:outline-none">
                            @error('mail_from_name') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">From Email Address</label>
                            <input wire:model="mail_from_address" type="email" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white focus:border-gray-900 focus:outline-none">
                            @error('mail_from_address') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- TextSMS Gateway Configurations -->
                <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm space-y-6 animate-fade-in">
                    <h2 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">TextSMS.co.ke Gateway Settings</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Gateway API URL</label>
                            <input wire:model="sms_gateway_url" type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white focus:border-gray-900 focus:outline-none">
                            @error('sms_gateway_url') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Partner ID</label>
                            <input wire:model="sms_partner_id" type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white focus:border-gray-900 focus:outline-none">
                            @error('sms_partner_id') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">API Key</label>
                            <input wire:model="sms_api_key" type="password" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white focus:border-gray-900 focus:outline-none">
                            @error('sms_api_key') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sender ID (Shortcode)</label>
                            <input wire:model="sms_sender_id" type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white focus:border-gray-900 focus:outline-none">
                            @error('sms_sender_id') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Custom Communication Templates -->
                <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm space-y-6 animate-fade-in">
                    <h2 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">Notification &amp; Alert Templates</h2>
                    
                    <div class="space-y-6 divide-y divide-gray-100">
                        <!-- Welcome Email -->
                        <div class="pt-4 first:pt-0 space-y-4">
                            <h3 class="text-xs font-bold text-blue-700 uppercase tracking-widest">1. Welcome Email (Mails)</h3>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400">Subject Line</label>
                                <input wire:model="mail_template_welcome_subject" type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white focus:border-gray-900 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400">Body Content (HTML Allowed, placeholders: {name})</label>
                                <textarea wire:model="mail_template_welcome_body" rows="4" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-xs font-mono bg-white focus:border-gray-900 focus:outline-none"></textarea>
                            </div>
                        </div>

                        <!-- Payout M-Pesa Email -->
                        <div class="pt-6 space-y-4">
                            <h3 class="text-xs font-bold text-blue-700 uppercase tracking-widest">2. M-Pesa Payout Alerts (Mails)</h3>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400">Subject Line</label>
                                <input wire:model="mail_template_payout_subject" type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white focus:border-gray-900 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400">Body Content (HTML, placeholders: {name}, {amount}, {phone}, {transaction_id})</label>
                                <textarea wire:model="mail_template_payout_body" rows="4" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-xs font-mono bg-white focus:border-gray-900 focus:outline-none"></textarea>
                            </div>
                        </div>

                        <!-- Fraud Warning Email -->
                        <div class="pt-6 space-y-4">
                            <h3 class="text-xs font-bold text-blue-700 uppercase tracking-widest">3. Fraud &amp; Quality Warning (Mails)</h3>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400">Subject Line</label>
                                <input wire:model="mail_template_fraud_subject" type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white focus:border-gray-900 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400">Body Content (HTML, placeholders: {name}, {survey}, {reason})</label>
                                <textarea wire:model="mail_template_fraud_body" rows="4" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-xs font-mono bg-white focus:border-gray-900 focus:outline-none"></textarea>
                            </div>
                        </div>

                        <!-- SMS Templates -->
                        <div class="pt-6 space-y-4">
                            <h3 class="text-xs font-bold text-green-700 uppercase tracking-widest">4. Gateway SMS Templates</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400">OTP Code Verification SMS (placeholders: {code}, {expiry})</label>
                                    <textarea wire:model="sms_template_otp" rows="2" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-xs bg-white focus:border-gray-900 focus:outline-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400">M-Pesa Payout SMS Alert (placeholders: {amount}, {ref})</label>
                                    <textarea wire:model="sms_template_payout" rows="2" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-xs bg-white focus:border-gray-900 focus:outline-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400">New Campaign Alert SMS (placeholders: {title}, {amount})</label>
                                    <textarea wire:model="sms_template_new_survey" rows="2" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-xs bg-white focus:border-gray-900 focus:outline-none"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Info & Action -->
            <div class="space-y-6">
                <div class="bg-gray-900 border border-gray-950 p-6 rounded-lg shadow-lg text-white space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider">Enterprise Configurations</h3>
                    <p class="text-xs text-gray-300 leading-relaxed">Adjusting these parameters will update global configurations instantly across all public and internal workspaces, including Government and NGO portals.</p>
                </div>

                <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm">
                    <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                        Save Configurations
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>
