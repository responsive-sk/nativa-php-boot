<?php

declare(strict_types=1);

namespace Application\DTOs;

use Application\Validation\Validator;

/**
 * Submit Contact Command DTO
 */
class SubmitContactCommand
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $message,
        public readonly ?string $subject = null,
    ) {
        $this->validate();
    }

    /**
     * Validate the command data
     *
     * @throws \Application\Exceptions\ValidationException
     */
    private function validate(): void
    {
        Validator::validate([
            'name' => $this->name,
            'email' => $this->email,
            'message' => $this->message,
            'subject' => $this->subject,
        ], [
            'name' => ['required', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'min:10', 'max:5000'],
            'subject' => ['max:255'],
        ]);
    }
}
