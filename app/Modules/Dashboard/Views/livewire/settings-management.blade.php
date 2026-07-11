@section('page_title', 'System Settings')

<div class="space-y-8 animate-fade-in">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">System Configurations</h1>
        <p class="text-sm text-gray-500">Manage branding details, SEO meta definitions, Google Analytics tracking scripts, and platform configurations independently.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left & Middle: Settings Cards -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- General Settings Section (Branding, SEO, Security) -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm space-y-6 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-base font-bold text-gray-900">General &amp; Branding Settings</h2>
                    <p class="text-xs text-gray-500 mt-1">Configure your corporate identity, favicon, custom footer, UI theme modes, and support details.</p>
                </div>

                <div class="p-6 space-y-6">
                    @if (session()->has('success_general'))
                        <div class="p-3 bg-green-50 border border-green-200 text-green-700 text-xs rounded-md">
                            {{ session('success_general') }}
                        </div>
                    @endif

                    <!-- Branding fields -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="sm:col-span-2">
                            <label for="site_title" class="block text-sm font-medium text-gray-700">Platform Title</label>
                            <input wire:model="site_title" type="text" id="site_title" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                            @error('site_title') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Site Logo -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Site Logo</label>
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-32 border border-gray-200 rounded flex items-center justify-center bg-gray-50 overflow-hidden">
                                    @if ($site_logo && $logoTemp = $this->getTemporaryUrl($site_logo))
                                        <img src="{{ $logoTemp }}" class="h-8 object-contain">
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
                                    @if ($site_favicon && $favTemp = $this->getTemporaryUrl($site_favicon))
                                        <img src="{{ $favTemp }}" class="h-6 w-6 object-contain">
                                    @else
                                        <img src="{{ asset($faviconPath) }}" class="h-6 w-6 object-contain" onerror="this.onerror=null; this.src='/favicon.png';">
                                    @endif
                                </div>
                                <input wire:model="site_favicon" type="file" id="site_favicon" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xxs file:font-semibold file:bg-gray-900 file:text-white hover:file:bg-gray-800">
                            </div>
                            @error('site_favicon') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Hero Background Image -->
                        <div class="sm:col-span-2 space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Hero Background Image</label>
                            <div class="flex items-center gap-4">
                                <div class="h-16 w-32 border border-gray-200 rounded flex items-center justify-center bg-gray-50 overflow-hidden relative">
                                    @if ($hero_background_image && $heroTemp = $this->getTemporaryUrl($hero_background_image))
                                        <img src="{{ $heroTemp }}" class="w-full h-full object-cover">
                                    @else
                                        <img src="{{ asset($heroBgPath) }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='/images/hero_data_analytics.png';">
                                    @endif
                                </div>
                                <div class="space-y-1">
                                    <input wire:model="hero_background_image" type="file" id="hero_background_image" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xxs file:font-semibold file:bg-gray-900 file:text-white hover:file:bg-gray-800">
                                    <p class="text-xxs text-gray-400 mt-1">Recommended: Wide landscape image representing clean, minimal data analytics (no humans, maximum 2MB).</p>
                                </div>
                            </div>
                            @error('hero_background_image') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="site_description" class="block text-sm font-medium text-gray-700">Meta Site Description</label>
                            <textarea wire:model="site_description" id="site_description" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"></textarea>
                            @error('site_description') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="site_footer" class="block text-sm font-medium text-gray-700">Footer Attribution Text</label>
                            <input wire:model="site_footer" type="text" id="site_footer" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                            @error('site_footer') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- SEO, Theme & Scripts -->
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

                        <!-- Security & Support fields -->
                        <div>
                            <label for="support_email" class="block text-sm font-medium text-gray-700">Support Email Address</label>
                            <input wire:model="support_email" type="email" id="support_email" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                            @error('support_email') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="otp_expiry" class="block text-sm font-medium text-gray-700">OTP Expiry (Minutes)</label>
                            <input wire:model="otp_expiry" type="number" id="otp_expiry" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                            @error('otp_expiry') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="rate_limit_login" class="block text-sm font-medium text-gray-700">Login Rate Limit (Attempts / Minute)</label>
                            <input wire:model="rate_limit_login" type="number" id="rate_limit_login" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
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

                <div class="p-6 border-t border-gray-100 bg-gray-50 flex justify-end">
                    <button type="button" wire:click="saveGeneral" class="inline-flex justify-center items-center rounded-md bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                        Save General Settings
                    </button>
                </div>
            </div>

            <!-- SMTP Mail Configurations -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm space-y-6 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-base font-bold text-gray-900">SMTP Mail Server Settings</h2>
                    <p class="text-xs text-gray-500 mt-1">Configure your SMTP hosts, connection credentials, and run instant mail dispatch tests.</p>
                </div>

                <div class="p-6 space-y-6">
                    @if (session()->has('success_smtp'))
                        <div class="p-3 bg-green-50 border border-green-200 text-green-700 text-xs rounded-md">
                            {{ session('success_smtp') }}
                        </div>
                    @endif

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
                            <input wire:model="mail_password" type="password" autocomplete="new-password" placeholder="Leave blank to keep existing password" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white focus:border-gray-900 focus:outline-none">
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

                    <!-- SMTP Mail Testing Interface -->
                    <div class="border-t border-gray-150 pt-6 space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500">SMTP Connection Tester</h3>
                        <p class="text-xxs text-gray-400 leading-relaxed">Enter a test recipient email address below to verify your SMTP parameters. The system will dynamically test using the parameters filled above.</p>
                        
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="flex-grow">
                                <input wire:model="testEmail" type="email" placeholder="test-recipient@metricapolls.com" class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white focus:border-gray-900 focus:outline-none">
                                @error('testEmail') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <button type="button" wire:click="sendTestMail" class="inline-flex items-center justify-center rounded-md bg-gray-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-550 transition whitespace-nowrap">
                                Send Test Email
                            </button>
                        </div>

                        @if (session()->has('success_test_mail'))
                            <div class="p-3 bg-green-50 border border-green-200 text-green-700 text-xs rounded-md">
                                {{ session('success_test_mail') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="p-6 border-t border-gray-100 bg-gray-50 flex justify-end">
                    <button type="button" wire:click="saveSmtp" class="inline-flex justify-center items-center rounded-md bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                        Save SMTP Credentials
                    </button>
                </div>
            </div>

            <!-- TextSMS Gateway Configurations -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm space-y-6 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-base font-bold text-gray-900">TextSMS.co.ke Gateway Settings</h2>
                    <p class="text-xs text-gray-500 mt-1">Configure your TextSMS Partner IDs, shortcode Sender IDs, and API keys for system alert messages.</p>
                </div>

                <div class="p-6 space-y-6">
                    @if (session()->has('success_sms'))
                        <div class="p-3 bg-green-50 border border-green-200 text-green-700 text-xs rounded-md">
                            {{ session('success_sms') }}
                        </div>
                    @endif

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

                <div class="p-6 border-t border-gray-100 bg-gray-50 flex justify-end">
                    <button type="button" wire:click="saveSms" class="inline-flex justify-center items-center rounded-md bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                        Save SMS Gateway
                    </button>
                </div>
            </div>

            <!-- Custom Communication Templates -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm space-y-6 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-base font-bold text-gray-900">Notification &amp; Alert Templates</h2>
                    <p class="text-xs text-gray-500 mt-1">Configure default email subjects, bodies, and gateway SMS notifications dispatched for system triggers.</p>
                </div>

                <div class="p-6 space-y-6">
                    @if (session()->has('success_templates'))
                        <div class="p-3 bg-green-50 border border-green-200 text-green-700 text-xs rounded-md">
                            {{ session('success_templates') }}
                        </div>
                    @endif

                    <div class="space-y-6 divide-y divide-gray-100">
                        <!-- Welcome Email -->
                        <div class="pt-4 first:pt-0 space-y-4">
                            <h3 class="text-xs font-bold text-blue-700 uppercase tracking-widest">1. Welcome Email (Mails)</h3>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400">Subject Line</label>
                                <input wire:model="mail_template_welcome_subject" type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white focus:border-gray-900 focus:outline-none">
                                @error('mail_template_welcome_subject') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400">Body Content (HTML Allowed, placeholders: {name})</label>
                                <textarea wire:model="mail_template_welcome_body" rows="4" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-xs font-mono bg-white focus:border-gray-900 focus:outline-none"></textarea>
                                @error('mail_template_welcome_body') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Payout M-Pesa Email -->
                        <div class="pt-6 space-y-4">
                            <h3 class="text-xs font-bold text-blue-700 uppercase tracking-widest">2. M-Pesa Payout Alerts (Mails)</h3>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400">Subject Line</label>
                                <input wire:model="mail_template_payout_subject" type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white focus:border-gray-900 focus:outline-none">
                                @error('mail_template_payout_subject') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400">Body Content (HTML, placeholders: {name}, {amount}, {phone}, {transaction_id})</label>
                                <textarea wire:model="mail_template_payout_body" rows="4" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-xs font-mono bg-white focus:border-gray-900 focus:outline-none"></textarea>
                                @error('mail_template_payout_body') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Fraud Warning Email -->
                        <div class="pt-6 space-y-4">
                            <h3 class="text-xs font-bold text-blue-700 uppercase tracking-widest">3. Fraud &amp; Quality Warning (Mails)</h3>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400">Subject Line</label>
                                <input wire:model="mail_template_fraud_subject" type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white focus:border-gray-900 focus:outline-none">
                                @error('mail_template_fraud_subject') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400">Body Content (HTML, placeholders: {name}, {survey}, {reason})</label>
                                <textarea wire:model="mail_template_fraud_body" rows="4" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-xs font-mono bg-white focus:border-gray-900 focus:outline-none"></textarea>
                                @error('mail_template_fraud_body') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- SMS Templates -->
                        <div class="pt-6 space-y-4">
                            <h3 class="text-xs font-bold text-green-700 uppercase tracking-widest">4. Gateway SMS Templates</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400">OTP Code Verification SMS (placeholders: {code}, {expiry})</label>
                                    <textarea wire:model="sms_template_otp" rows="2" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-xs bg-white focus:border-gray-900 focus:outline-none"></textarea>
                                    @error('sms_template_otp') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400">M-Pesa Payout SMS Alert (placeholders: {amount}, {ref})</label>
                                    <textarea wire:model="sms_template_payout" rows="2" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-xs bg-white focus:border-gray-900 focus:outline-none"></textarea>
                                    @error('sms_template_payout') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400">New Campaign Alert SMS (placeholders: {title}, {amount})</label>
                                    <textarea wire:model="sms_template_new_survey" rows="2" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-xs bg-white focus:border-gray-900 focus:outline-none"></textarea>
                                    @error('sms_template_new_survey') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-gray-100 bg-gray-50 flex justify-end">
                    <button type="button" wire:click="saveTemplates" class="inline-flex justify-center items-center rounded-md bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                        Save Communication Templates
                    </button>
                </div>
            </div>

            <!-- Legal & Regulatory Pages -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm space-y-6 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-base font-bold text-gray-900">Legal &amp; Regulatory Pages</h2>
                    <p class="text-xs text-gray-500 mt-1">Update regulatory policies, GDPR declarations, and researcher code of conduct items.</p>
                </div>

                <div class="p-6 space-y-6">
                    @if (session()->has('success_legal'))
                        <div class="p-3 bg-green-50 border border-green-200 text-green-700 text-xs rounded-md">
                            {{ session('success_legal') }}
                        </div>
                    @endif

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Terms of Service Document (HTML Allowed)</label>
                            <textarea wire:model="terms_of_service" rows="10" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-xs font-mono bg-white focus:border-gray-900 focus:outline-none"></textarea>
                            @error('terms_of_service') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Privacy Policy Document (HTML Allowed)</label>
                            <textarea wire:model="privacy_policy" rows="10" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-xs font-mono bg-white focus:border-gray-900 focus:outline-none"></textarea>
                            @error('privacy_policy') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-gray-100 bg-gray-50 flex justify-end">
                    <button type="button" wire:click="saveLegal" class="inline-flex justify-center items-center rounded-md bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                        Save Legal Documents
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Column: Info Panel -->
        <div class="space-y-6">
            <!-- Sign In & Authentication Options Card -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm space-y-6 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-base font-bold text-gray-900">Authentication Options</h2>
                    <p class="text-xs text-gray-500 mt-1">Enable or disable authentication channels for public portal panelists.</p>
                </div>

                <div class="p-6 space-y-6">
                    @if (session()->has('success_auth_types'))
                        <div class="p-3 bg-green-50 border border-green-200 text-green-700 text-xs rounded-md">
                            {{ session('success_auth_types') }}
                        </div>
                    @endif
                    @if ($errors->has('auth_methods'))
                        <div class="p-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-md">
                            {{ $errors->first('auth_methods') }}
                        </div>
                    @endif

                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex h-5 items-center">
                                <input wire:model="login_google_enabled" type="checkbox" id="login_google_enabled" class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900 bg-white">
                            </div>
                            <div class="ml-3 text-xs">
                                <label for="login_google_enabled" class="font-bold text-gray-700">Google Account SSO</label>
                                <p class="text-gray-500 mt-0.5">Allow users to register and sign in securely using Google OAuth.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex h-5 items-center">
                                <input wire:model="login_email_enabled" type="checkbox" id="login_email_enabled" class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900 bg-white">
                            </div>
                            <div class="ml-3 text-xs">
                                <label for="login_email_enabled" class="font-bold text-gray-700">Email &amp; Password Form</label>
                                <p class="text-gray-500 mt-0.5">Allow panelist log ins using traditional email credentials with secondary OTP codes.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex h-5 items-center">
                                <input wire:model="login_sms_enabled" type="checkbox" id="login_sms_enabled" class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900 bg-white">
                            </div>
                            <div class="ml-3 text-xs">
                                <label for="login_sms_enabled" class="font-bold text-gray-700">Phone &amp; SMS OTP</label>
                                <p class="text-gray-500 mt-0.5">Allow sign ins using registered phone numbers and secure SMS codes.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-gray-150 bg-gray-50 flex justify-end">
                    <button type="button" wire:click="saveAuthTypes" class="inline-flex justify-center items-center rounded-md bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                        Save Sign In Options
                    </button>
                </div>
            </div>

            <!-- Google OAuth Credentials Config Card -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden mt-6">
                <div class="p-6 border-b border-gray-150">
                    <h2 class="text-sm font-bold text-gray-900 font-sans">Google API SSO Credentials</h2>
                    <p class="text-xs text-gray-500 mt-1">Configure OAuth 2.0 Credentials from your Google Cloud Console for secure authentication.</p>
                </div>
                
                @if (session()->has('success_google_login'))
                    <div class="px-6 pt-4">
                        <div class="p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-md shadow-sm">
                            {{ session('success_google_login') }}
                        </div>
                    </div>
                @endif

                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="google_client_id" class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Google Client ID</label>
                            <input wire:model="google_client_id" type="text" id="google_client_id" placeholder="e.g. 1234567890-abc123xyz.apps.googleusercontent.com" class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2.5 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                            @error('google_client_id') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="google_client_secret" class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Google Client Secret</label>
                            <input wire:model="google_client_secret" type="password" id="google_client_secret" placeholder="••••••••••••••••••••••••••••••••" class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2.5 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                            @error('google_client_secret') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="google_redirect_url" class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Authorized Redirect URI</label>
                            <input wire:model="google_redirect_url" type="text" id="google_redirect_url" class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2.5 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                            <p class="text-xxs text-gray-400 mt-1">This URI must match the Authorized Redirect URIs in your Google Cloud Console Credentials.</p>
                            @error('google_redirect_url') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-gray-150 bg-gray-50 flex justify-end">
                    <button type="button" wire:click="saveGoogleLogin" class="inline-flex justify-center items-center rounded-md bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                        Save Google API Credentials
                    </button>
                </div>
            </div>

            <div class="bg-gray-900 border border-gray-950 p-6 rounded-lg shadow-lg text-white space-y-4 mt-6">
                <h3 class="text-sm font-bold uppercase tracking-wider">Independent Modules</h3>
                <p class="text-xs text-gray-300 leading-relaxed font-sans">Each configuration module is now completely isolated. You can update and save your SMTP server credentials without filling in the rest of the site properties, descriptions, or templates.</p>
            </div>
        </div>

    </div>
</div>
