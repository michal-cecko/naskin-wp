<?php

namespace Theme\Mail;

use Saurus\App\Modules\Mail\Mailable;

class DefaultEmail extends Mailable
{
    public function __construct(public string $subject, public string $content){}

    public function data(): array
    {
        return [
            'content' => $this->content,
        ];
    }

    public function html(): string
    {
        return templates()->generate('emails.default', $this->data());
    }
}