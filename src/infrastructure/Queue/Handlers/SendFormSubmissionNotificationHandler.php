<?php

declare(strict_types=1);

namespace Infrastructure\Queue\Handlers;

use Infrastructure\Queue\Entities\Job;

/**
 * Send Form Submission Notification Handler
 * Sends email notification when a form is submitted
 */
class SendFormSubmissionNotificationHandler
{
    public function __invoke(Job $job): void
    {
        $data = $job->payload()['data'] ?? [];
        
        $formName = $data['form_name'] ?? 'Contact Form';
        $formSlug = $data['form_slug'] ?? '';
        $submissionData = $data['submission'] ?? [];
        $submittedAt = $data['submitted_at'] ?? date('Y-m-d H:i:s');
        
        // Build email content
        $subject = "New Submission: {$formName}";
        
        $message = "New form submission received!\n\n";
        $message .= "Form: {$formName}\n";
        $message .= "Submitted: {$submittedAt}\n\n";
        $message .= "Submission Details:\n";
        $message .= str_repeat('-', 40) . "\n";
        
        foreach ($submissionData as $key => $value) {
            $message .= ucfirst($key) . ": " . (is_array($value) ? implode(', ', $value) : $value) . "\n";
        }
        
        // Log the email (in production, use proper mailer)
        error_log(sprintf(
            "[FORM SUBMISSION] %s\n%s",
            $subject,
            $message
        ));
        
        // TODO: Implement proper email sending
        // Example with Symfony Mailer:
        // $mailer->send(new EmailMessage(
        //     to: $_ENV['ADMIN_EMAIL'] ?? 'admin@example.com',
        //     subject: $subject,
        //     body: $message
        // ));
        
        // Or use PHP mail():
        // $to = $_ENV['ADMIN_EMAIL'] ?? 'admin@example.com';
        // $headers = "From: noreply@phpcms.local\r\n";
        // mail($to, $subject, $message, $headers);
    }
}
