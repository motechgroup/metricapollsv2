<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            'site_title' => 'Metrica Polls',
            'site_logo' => 'images/logo.png',
            'site_favicon' => 'favicon.png',
            'site_footer' => '&copy; ' . date('Y') . ' Metrica Polls. All rights reserved. Registered Enterprise Marketing & Research Firm.',
            'site_description' => 'Enterprise Marketing & Research Firm for Africa. Built to support millions of respondents with offline and online data collection.',
            'site_theme' => 'Light',
            'maintenance_mode' => '0',
            'site_seo_keywords' => 'market research africa, survey analytics parastatals, nairobi consumer opinion, kampala product feasibility, east africa polling',
            'analytics_code' => '<!-- Google Analytics Mock Tracking Script -->',
            'support_email' => 'support@metricapolls.com',
            'otp_expiry' => '15',
            'enable_audit_logs' => '1',
            'rate_limit_login' => '5',

            // SMTP Configurations
            'mail_host' => 'smtp.mailtrap.io',
            'mail_port' => '2525',
            'mail_username' => 'mock_username',
            'mail_password' => 'mock_password',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'noreply@metricapolls.com',
            'mail_from_name' => 'Metrica Polls',

            // TextSMS Gateway
            'sms_gateway_url' => 'https://sms.textsms.co.ke/api/services/sendsms/',
            'sms_api_key' => 'mock_textsms_api_key_123',
            'sms_partner_id' => 'mock_partner_id_789',
            'sms_sender_id' => 'METRICA',

            // Mail Templates
            'mail_template_welcome_subject' => 'Welcome to Metrica Polls Panel!',
            'mail_template_welcome_body' => "<h3>Welcome to Metrica Polls, {name}!</h3>\n<p>Thank you for registering with Metrica Polls, East Africa's leading Marketing & Research Firm.</p>\n<p>Get started by taking our mandatory <strong>Analyst Qualification Training Test</strong> to verify your account and earn your Bronze Badge.</p>\n<p>Once verified, you will unlock access to high-paying consumer surveys rewarded directly to your M-Pesa account.</p>\n<p>Happy polling!</p>",
            
            'mail_template_payout_subject' => 'M-Pesa Payout Initiated - Metrica Polls',
            'mail_template_payout_body' => "<h3>Hello {name},</h3>\n<p>We have successfully processed your wallet withdrawal request.</p>\n<p>An amount of <strong>KES {amount}</strong> has been disbursed to your registered M-Pesa number: {phone}.</p>\n<p>Reference: {transaction_id}.</p>\n<p>Thank you for submitting high-quality research responses!</p>",
            
            'mail_template_fraud_subject' => 'Survey Alert: Response Quality Warning',
            'mail_template_fraud_body' => "<h3>Data Quality Warning</h3>\n<p>Hello {name},</p>\n<p>Our Anti-Fraud Security Engine flagged your recent response for survey '{survey}' due to: <strong>{reason}</strong>.</p>\n<p>Please note that multi-accounting, copy-paste answers, or completing surveys faster than human reading limits violates our researcher code of conduct.</p>\n<p>Future violations may lead to panelist suspension.</p>",

            // SMS Templates
            'sms_template_otp' => 'Your Metrica Polls verification OTP code is {code}. Expiry in {expiry} mins.',
            'sms_template_payout' => 'Metrica Polls: We have sent KES {amount} to your M-Pesa wallet. Ref: {ref}. Thank you for your feedback!',
            'sms_template_new_survey' => "Metrica Polls: A new paid survey '{title}' paying KES {amount} is now open for your badge level! Log in to respond.",

            // Terms & Privacy
            'terms_of_service' => "<h2>1. Introduction</h2>\n<p>Welcome to Metrica Polls. These Terms of Service govern your use of our marketing and research platform, including online surveys, focus group recruitment, and data analytics services across East Africa.</p>\n\n<h2>2. Researcher Accounts & Panelist Registration</h2>\n<p>Panelists must be at least 18 years old and reside in Kenya, Uganda, Tanzania, or Rwanda. Panelists register exclusively using Google Account single sign-on. Multiple accounts per individual are strictly prohibited and will result in wallet suspension.</p>\n\n<h2>3. Anti-Fraud & Response Quality Standards</h2>\n<p>To ensure high-fidelity insights for corporate and NGO partners, we monitor response quality. Panelists must complete campaigns with integrity:\n<ul>\n  <li>Speed Trap: Completing surveys faster than normal reading speeds will flag the profile.</li>\n  <li>Attention Checks: Mismatched attention check answers will invalidate submissions.</li>\n  <li>Copy-Paste Audit: Automated duplication verification blocks copy-pasted textual answers.</li>\n</ul>\nViolation of these policies will lead to instant point forfeiture.</p>\n\n<h2>4. Wallet Payouts & MPesa Disbursements</h2>\n<p>Earnings are accumulated as points (1 point = 1 KES) and can be redeemed for M-Pesa mobile money disbursements once the minimum threshold is met. Payments are subject to gateway provider terms.</p>\n\n<h2>5. Intellectual Property</h2>\n<p>All survey designs, reports, and AI analytics models are the property of Metrica Polls or its licensed client organizations.</p>",
            'privacy_policy' => "<h2>1. Data Collection & Processing</h2>\n<p>We collect information you provide directly to us when registering via Google Login, completing demographic profiles, and responding to survey campaigns. This includes your name, email, phone number, location, gender, age, and occupation.</p>\n\n<h2>2. How We Use Your Data</h2>\n<p>We process responses to compile aggregated marketing research intelligence reports. Your personally identifiable information (PII) is encrypted and stripped from datasets sold to corporate clients, NGO partners, or parastatals.</p>\n\n<h2>3. Data Sharing & Gateway Partners</h2>\n<p>We share your mobile number with mobile money gateways (such as Safaricom M-Pesa) solely to execute wallet redemption disbursements. We do not sell or lease your telephone contact lists to third-party telemarketers.</p>\n\n<h2>4. Security Measures & ISO Standards</h2>\n<p>We implement strict administrative, technical, and physical safeguards to protect database records from unauthorized access, loss, or alteration. All client briefs and surveys are encrypted in transit and at rest.</p>\n\n<h2>5. Your Rights & GDPR Compliance</h2>\n<p>Panelists retain the right to request profile deletion, inspect stored responses, or withdraw consent at any time. To request data deletion, contact support@metricapolls.com.</p>",
        ];

        foreach ($defaults as $key => $value) {
            Setting::setValue($key, $value);
        }
    }
}
