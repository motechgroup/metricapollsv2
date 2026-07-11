<?php

namespace App\Modules\Dashboard\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Setting;
use Livewire\Attributes\Title;

#[Title('System Settings - Metrica Polls')]
class SettingsManagement extends Component
{
    use WithFileUploads;

    // Site settings properties
    public $site_title;
    public $site_logo; // Uploaded file
    public $site_favicon; // Uploaded file
    public $site_footer;
    public $site_description;
    public $site_theme = 'Light';
    public $maintenance_mode = false;
    public $site_seo_keywords;
    public $analytics_code;

    // Original properties (compatibility)
    public $support_email;
    public $otp_expiry;
    public $enable_audit_logs = true;
    public $rate_limit_login;

    // Path visual placeholders
    public $logoPath;
    public $faviconPath;

    // SMTP properties
    public $mail_host;
    public $mail_port;
    public $mail_username;
    public $mail_password;
    public $mail_encryption;
    public $mail_from_address;
    public $mail_from_name;

    // TextSMS properties
    public $sms_gateway_url;
    public $sms_api_key;
    public $sms_partner_id;
    public $sms_sender_id;

    // Mail templates properties
    public $mail_template_welcome_subject;
    public $mail_template_welcome_body;
    public $mail_template_payout_subject;
    public $mail_template_payout_body;
    public $mail_template_fraud_subject;
    public $mail_template_fraud_body;

    // SMS templates properties
    public $sms_template_otp;
    public $sms_template_payout;
    public $sms_template_new_survey;

    // Legal Pages properties
    public $terms_of_service;
    public $privacy_policy;

    // Test Mail properties
    public $testEmail = '';

    public function mount()
    {
        // Resolve settings from Database via Setting helper
        $this->site_title = Setting::getValue('site_title', 'Metrica Polls');
        $this->site_footer = Setting::getValue('site_footer', '');
        $this->site_description = Setting::getValue('site_description', '');
        $this->site_theme = Setting::getValue('site_theme', 'Light');
        $this->maintenance_mode = Setting::getValue('maintenance_mode', '0') === '1';
        $this->site_seo_keywords = Setting::getValue('site_seo_keywords', '');
        $this->analytics_code = Setting::getValue('analytics_code', '');

        // Original properties (compatibility)
        $this->support_email = Setting::getValue('support_email', 'support@metricapolls.com');
        $this->otp_expiry = (int) Setting::getValue('otp_expiry', 15);
        $this->enable_audit_logs = Setting::getValue('enable_audit_logs', '1') === '1';
        $this->rate_limit_login = (int) Setting::getValue('rate_limit_login', 5);

        // Path references
        $this->logoPath = Setting::getValue('site_logo', 'images/logo.png');
        $this->faviconPath = Setting::getValue('site_favicon', 'favicon.png');

        // SMTP configuration
        $this->mail_host = Setting::getValue('mail_host', 'smtp.mailtrap.io');
        $this->mail_port = (int) Setting::getValue('mail_port', 2525);
        $this->mail_username = Setting::getValue('mail_username', '');
        $this->mail_password = Setting::getValue('mail_password', '');
        $this->mail_encryption = Setting::getValue('mail_encryption', 'tls');
        $this->mail_from_address = Setting::getValue('mail_from_address', 'noreply@metricapolls.com');
        $this->mail_from_name = Setting::getValue('mail_from_name', 'Metrica Polls');

        // TextSMS configuration
        $this->sms_gateway_url = Setting::getValue('sms_gateway_url', 'https://sms.textsms.co.ke/api/services/sendsms/');
        $this->sms_api_key = Setting::getValue('sms_api_key', '');
        $this->sms_partner_id = Setting::getValue('sms_partner_id', '');
        $this->sms_sender_id = Setting::getValue('sms_sender_id', 'METRICA');

        // Mail templates
        $this->mail_template_welcome_subject = Setting::getValue('mail_template_welcome_subject', 'Welcome to Metrica Polls Panel!');
        $this->mail_template_welcome_body = Setting::getValue('mail_template_welcome_body', '');
        $this->mail_template_payout_subject = Setting::getValue('mail_template_payout_subject', 'M-Pesa Payout Initiated - Metrica Polls');
        $this->mail_template_payout_body = Setting::getValue('mail_template_payout_body', '');
        $this->mail_template_fraud_subject = Setting::getValue('mail_template_fraud_subject', 'Survey Alert: Response Quality Warning');
        $this->mail_template_fraud_body = Setting::getValue('mail_template_fraud_body', '');

        // SMS templates
        $this->sms_template_otp = Setting::getValue('sms_template_otp', 'Your Metrica Polls verification OTP code is {code}. Expiry in {expiry} mins.');
        $this->sms_template_payout = Setting::getValue('sms_template_payout', 'Metrica Polls: We have sent KES {amount} to your M-Pesa wallet. Ref: {ref}. Thank you for your feedback!');
        $this->sms_template_new_survey = Setting::getValue('sms_template_new_survey', "Metrica Polls: A new paid survey '{title}' paying KES {amount} is now open for your badge level! Log in to respond.");

        // Legal Pages
        $this->terms_of_service = Setting::getValue('terms_of_service', '');
        $this->privacy_policy = Setting::getValue('privacy_policy', '');
    }

    public function saveGeneral()
    {
        $this->validate([
            'site_title' => 'required|string|max:255',
            'support_email' => 'required|email|max:255',
            'otp_expiry' => 'required|integer|min:1|max:120',
            'enable_audit_logs' => 'required|boolean',
            'rate_limit_login' => 'required|integer|min:1|max:100',
            'site_footer' => 'required|string|max:500',
            'site_description' => 'required|string|max:1000',
            'site_theme' => 'required|string|in:Light,Dark,Glassmorphism',
            'maintenance_mode' => 'required|boolean',
            'site_seo_keywords' => 'nullable|string',
            'analytics_code' => 'nullable|string',
            'site_logo' => 'nullable|image|max:1024', // max 1MB
            'site_favicon' => 'nullable|image|max:512', // max 512KB
        ]);

        // Process uploaded logo
        if ($this->site_logo) {
            $filename = 'logo_' . time() . '.' . $this->site_logo->getClientOriginalExtension();
            $this->site_logo->storeAs('public/images', $filename);
            $this->logoPath = 'storage/images/' . $filename;
            Setting::setValue('site_logo', $this->logoPath);
        }

        // Process uploaded favicon
        if ($this->site_favicon) {
            $filename = 'favicon_' . time() . '.' . $this->site_favicon->getClientOriginalExtension();
            $this->site_favicon->storeAs('public', $filename);
            $this->faviconPath = 'storage/' . $filename;
            Setting::setValue('site_favicon', $this->faviconPath);
        }

        Setting::setValue('site_title', $this->site_title);
        Setting::setValue('support_email', $this->support_email);
        Setting::setValue('otp_expiry', (string) $this->otp_expiry);
        Setting::setValue('enable_audit_logs', $this->enable_audit_logs ? '1' : '0');
        Setting::setValue('rate_limit_login', (string) $this->rate_limit_login);
        Setting::setValue('site_footer', $this->site_footer);
        Setting::setValue('site_description', $this->site_description);
        Setting::setValue('site_theme', $this->site_theme);
        Setting::setValue('maintenance_mode', $this->maintenance_mode ? '1' : '0');
        Setting::setValue('site_seo_keywords', $this->site_seo_keywords ?? '');
        Setting::setValue('analytics_code', $this->analytics_code ?? '');

        session()->flash('success_general', 'General settings saved successfully.');
    }

    public function saveSmtp()
    {
        $this->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'required|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);

        Setting::setValue('mail_host', $this->mail_host);
        Setting::setValue('mail_port', (string) $this->mail_port);
        Setting::setValue('mail_username', $this->mail_username ?? '');
        Setting::setValue('mail_password', $this->mail_password ?? '');
        Setting::setValue('mail_encryption', $this->mail_encryption);
        Setting::setValue('mail_from_address', $this->mail_from_address);
        Setting::setValue('mail_from_name', $this->mail_from_name);

        session()->flash('success_smtp', 'SMTP credentials saved successfully.');
    }

    public function sendTestMail()
    {
        $this->validate([
            'testEmail' => 'required|email',
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_encryption' => 'required|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);

        try {
            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.host' => $this->mail_host,
                'mail.mailers.smtp.port' => $this->mail_port,
                'mail.mailers.smtp.username' => $this->mail_username,
                'mail.mailers.smtp.password' => $this->mail_password,
                'mail.mailers.smtp.encryption' => $this->mail_encryption,
                'mail.from.address' => $this->mail_from_address,
                'mail.from.name' => $this->mail_from_name,
            ]);

            // Force Laravel to rebuild the mailer with the unsaved config values
            if (app()->bound('mail.manager')) {
                app()->make('mail.manager')->forgetMailers();
            }

            \Illuminate\Support\Facades\Mail::to($this->testEmail)->send(
                new \App\Mail\CustomConfigurableMail(
                    "Metrica SMTP Test Mail Connection",
                    "Metrica Polls: This is a test email confirming that your SMTP connection settings are correctly configured on your live site!"
                )
            );

            session()->flash('success_test_mail', 'Test email sent successfully to ' . $this->testEmail . '!');
        } catch (\Throwable $e) {
            $this->addError('testEmail', 'SMTP Connection Failed: ' . $e->getMessage());
        }
    }

    public function saveSms()
    {
        $this->validate([
            'sms_gateway_url' => 'required|url',
            'sms_api_key' => 'nullable|string',
            'sms_partner_id' => 'nullable|string',
            'sms_sender_id' => 'required|string',
        ]);

        Setting::setValue('sms_gateway_url', $this->sms_gateway_url);
        Setting::setValue('sms_api_key', $this->sms_api_key ?? '');
        Setting::setValue('sms_partner_id', $this->sms_partner_id ?? '');
        Setting::setValue('sms_sender_id', $this->sms_sender_id);

        session()->flash('success_sms', 'TextSMS configurations saved successfully.');
    }

    public function saveTemplates()
    {
        $this->validate([
            'mail_template_welcome_subject' => 'required|string',
            'mail_template_welcome_body' => 'required|string',
            'mail_template_payout_subject' => 'required|string',
            'mail_template_payout_body' => 'required|string',
            'mail_template_fraud_subject' => 'required|string',
            'mail_template_fraud_body' => 'required|string',
            'sms_template_otp' => 'required|string',
            'sms_template_payout' => 'required|string',
            'sms_template_new_survey' => 'required|string',
        ]);

        Setting::setValue('mail_template_welcome_subject', $this->mail_template_welcome_subject);
        Setting::setValue('mail_template_welcome_body', $this->mail_template_welcome_body);
        Setting::setValue('mail_template_payout_subject', $this->mail_template_payout_subject);
        Setting::setValue('mail_template_payout_body', $this->mail_template_payout_body);
        Setting::setValue('mail_template_fraud_subject', $this->mail_template_fraud_subject);
        Setting::setValue('mail_template_fraud_body', $this->mail_template_fraud_body);

        Setting::setValue('sms_template_otp', $this->sms_template_otp);
        Setting::setValue('sms_template_payout', $this->sms_template_payout);
        Setting::setValue('sms_template_new_survey', $this->sms_template_new_survey);

        session()->flash('success_templates', 'Communication templates saved successfully.');
    }

    public function saveLegal()
    {
        $this->validate([
            'terms_of_service' => 'required|string',
            'privacy_policy' => 'required|string',
        ]);

        Setting::setValue('terms_of_service', $this->terms_of_service);
        Setting::setValue('privacy_policy', $this->privacy_policy);

        session()->flash('success_legal', 'Legal pages updated successfully.');
    }

    public function render()
    {
        return view('Dashboard::livewire.settings-management')
            ->layout('Dashboard::admin-layout');
    }
}
