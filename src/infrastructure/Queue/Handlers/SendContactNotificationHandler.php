<?php

declare(strict_types=1);

namespace Infrastructure\Queue\Handlers;

use Infrastructure\Queue\Entities\Job;

/**
 * Send Contact Notification Handler
 * Sends email notification when contact form is submitted
 */
class SendContactNotificationHandler
{
    public function __invoke(Job $job): void
    {
        $data = $job->payload()['data'] ?? [];
        
        $name = $data['name'] ?? 'Unknown';
        $email = $data['email'] ?? '';
        $subject = $data['subject'] ?? 'Contact Form Submission';
        $message = $data['message'] ?? '';
        
        // Log the email (in production, use proper mailer)
        error_log(sprintf(
            "[CONTACT FORM] From: %s <%s>\nSubject: %s\nMessage: %s",
            $name,
            $email,
            $subject,
            $message
        ));
        
        // TODO: Implement proper email sending
        // Example with PHP mail():
        // $to = $_ENV['ADMIN_EMAIL'] ?? 'admin@example.com';
        // $headers = "From: {$name} <{$email}>\r\n";
        // $headers .= "Reply-To: {$email}\r\n";
        // mail($to, $subject, $message, $headers);
        
        // Or use Symfony Mailer, PHPMailer, etc.
    }
}
