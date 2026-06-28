<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SystemErrorMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $level;
    public string $levelClass;
    public string $shortMessage;
    public string $fullMessage;
    public ?string $stackTrace;
    public string $url;
    public string $method;
    public string $ip;
    public string $userAgent;
    public ?array $user;
    public array $input;
    public string $timestamp;

    public function __construct(array $data)
    {
        $this->level = $data['level'] ?? 'ERROR';
        $this->levelClass = match ($this->level) {
            'CRITICAL', 'EMERGENCY' => 'critical',
            'ERROR' => 'error',
            'WARNING', 'ALERT' => 'warning',
            default => 'error',
        };
        $this->shortMessage = $data['short_message'] ?? 'Error desconocido';
        $this->fullMessage = $data['message'] ?? '';
        $this->stackTrace = $data['trace'] ?? null;
        $this->url = $data['url'] ?? request()->fullUrl();
        $this->method = $data['method'] ?? request()->method();
        $this->ip = $data['ip'] ?? request()->ip();
        $this->userAgent = $data['user_agent'] ?? request()->userAgent() ?? '';
        $this->user = $data['user'] ?? (auth()->check() ? ['id' => auth()->id(), 'name' => auth()->user()->name, 'email' => auth()->user()->email] : null);
        $this->input = $data['input'] ?? request()->except(['password', 'password_confirmation', '_token']);
        $this->timestamp = $data['timestamp'] ?? now()->toDateTimeString();
    }

    public function build(): static
    {
        return $this->subject('[' . config('app.name') . '] ' . $this->level . ': ' . $this->shortMessage)
            ->view('emails.system-error');
    }
}
