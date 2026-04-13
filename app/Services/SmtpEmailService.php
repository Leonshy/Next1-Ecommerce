<?php

namespace App\Services;

use App\Models\EmailTemplate;
use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class SmtpEmailService
{
    public function sendFromTemplate(string $templateKey, string $to, array $variables = []): bool
    {
        $template = EmailTemplate::where('template_key', $templateKey)->where('is_active', true)->first();

        if (!$template) {
            Log::warning("Email template '{$templateKey}' not found or inactive.");
            return false;
        }

        $subject = $this->replaceVariables($template->subject, $variables);
        $html    = $this->replaceVariables($template->body_html, $variables);

        return $this->sendHtml($to, $subject, $html);
    }

    public function sendHtml(string $to, string $subject, string $html): bool
    {
        try {
            $this->configureMailer();

            Mail::html($html, function (Message $message) use ($to, $subject) {
                $smtp = SmtpSetting::first();
                $from = $smtp?->from_email ?? config('mail.from.address');
                $name = $smtp?->from_name ?? config('mail.from.name');

                $message->to($to)
                        ->subject($subject)
                        ->from($from, $name);
            });

            return true;
        } catch (\Throwable $e) {
            Log::error('Email send failed', ['error' => $e->getMessage(), 'to' => $to]);
            return false;
        }
    }

    private function configureMailer(): void
    {
        $smtp = SmtpSetting::first();

        if (!$smtp || !$smtp->is_active || !$smtp->host) return;

        config([
            'mail.default'              => 'smtp',
            'mail.mailers.smtp.host'    => $smtp->host,
            'mail.mailers.smtp.port'    => $smtp->port,
            'mail.mailers.smtp.encryption' => $smtp->encryption === 'none' ? null : $smtp->encryption,
            'mail.mailers.smtp.username' => $smtp->username,
            'mail.mailers.smtp.password' => $smtp->password,
            'mail.from.address'         => $smtp->from_email,
            'mail.from.name'            => $smtp->from_name,
        ]);
    }

    private function replaceVariables(string $text, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $text = str_replace('{' . $key . '}', $value, $text);
        }
        return $text;
    }
}
