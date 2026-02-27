<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Frontend;

use Application\Services\FormAppService;
use Infrastructure\Persistence\DatabaseConnection;
use Infrastructure\Persistence\UnitOfWork;
use Infrastructure\Persistence\Repositories\FormRepository;
use Infrastructure\Persistence\Repositories\FormSubmissionRepository;
use Domain\Model\FormSubmission;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Dynamic Form Controller
 */
class FormController
{
    private FormAppService $formService;

    public function __construct()
    {
        $db = new DatabaseConnection();
        $uow = new UnitOfWork($db);
        $formRepo = new FormRepository($uow);
        $this->formService = new FormAppService($formRepo);
    }

    public function show(string $slug): Response
    {
        $form = $this->formService->findBySlug($slug);

        if (!$form) {
            return new Response('Form not found', 404);
        }

        $schema = json_decode($form->schema(), true) ?? [];
        
        $fields = '';
        foreach ($schema as $field) {
            $fields .= $this->renderField($field);
        }

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$form->name()}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">{$form->name()}</h1>
            <p class="text-gray-600 mb-6">Please fill out the form below</p>
            
            <form id="dynamicForm" class="space-y-6">
                {$fields}
                
                <button type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                    Submit
                </button>
            </form>
            
            <div id="success" class="hidden mt-6 p-6 bg-green-50 border border-green-200 rounded-lg text-center">
                <svg class="mx-auto h-12 w-12 text-green-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <h3 class="text-lg font-medium text-green-800">Success!</h3>
                <p class="text-green-600 mt-1">{$form->successMessage()}</p>
            </div>
            
            <div id="error" class="hidden mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-600" id="errorMessage"></p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('dynamicForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const form = e.target;
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            
            // Handle checkboxes
            form.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                data[cb.name] = cb.checked;
            });
            
            try {
                const response = await fetch('/form/{$slug}', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data),
                });
                
                const result = await response.json();
                
                if (response.ok && result.success) {
                    form.classList.add('hidden');
                    document.getElementById('success').classList.remove('hidden');
                } else {
                    throw new Error(result.error || 'Something went wrong');
                }
            } catch (err) {
                document.getElementById('errorMessage').textContent = err.message;
                document.getElementById('error').classList.remove('hidden');
            }
        });
    </script>
</body>
</html>
HTML;

        return new Response($html);
    }

    public function submit(string $slug, Request $request): Response
    {
        try {
            $form = $this->formService->findBySlug($slug);

            if (!$form) {
                return new Response(json_encode(['error' => 'Form not found']), 404, ['Content-Type' => 'application/json']);
            }

            $data = json_decode($request->getContent(), true);
            
            // Validate against schema
            $schema = json_decode($form->schema(), true) ?? [];
            $errors = $this->validate($data, $schema);

            if (!empty($errors)) {
                return new Response(json_encode(['error' => implode(', ', $errors)]), 400, ['Content-Type' => 'application/json']);
            }

            // Save submission
            $db = new DatabaseConnection();
            $uow = new UnitOfWork($db);
            $submissionRepo = new FormSubmissionRepository($uow);
            
            $submission = FormSubmission::create(
                formId: $form->id(),
                data: $data,
                ipAddress: $request->getClientIp() ?? 'unknown',
                userAgent: $request->headers->get('User-Agent') ?? 'unknown',
            );
            
            $submissionRepo->save($submission);

            // TODO: Send email notification if configured

            return new Response(json_encode(['success' => true]), 200, ['Content-Type' => 'application/json']);
        } catch (\Throwable $e) {
            return new Response(json_encode(['error' => $e->getMessage()]), 500, ['Content-Type' => 'application/json']);
        }
    }

    private function validate(array $data, array $schema): array
    {
        $errors = [];
        
        foreach ($schema as $field) {
            $name = $field['name'];
            
            if ($field['required'] && empty($data[$name])) {
                $errors[] = "{$field['label']} is required";
            }
            
            if ($field['type'] === 'email' && !empty($data[$name])) {
                if (!filter_var($data[$name], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "{$field['label']} must be a valid email";
                }
            }
            
            if ($field['type'] === 'number' && !empty($data[$name])) {
                if (!is_numeric($data[$name])) {
                    $errors[] = "{$field['label']} must be a number";
                }
            }
        }
        
        return $errors;
    }

    private function renderField(array $field): string
    {
        $required = $field['required'] ? 'required' : '';
        $requiredMark = $field['required'] ? '<span class="text-red-500">*</span>' : '';
        
        switch ($field['type']) {
            case 'textarea':
                return <<<HTML
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {$field['label']} {$requiredMark}
                        </label>
                        <textarea name="{$field['name']}" {$required} rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="{$field['placeholder']}"></textarea>
                    </div>
                HTML;
                
            case 'select':
                $options = array_map('trim', explode(',', $field['options'] ?? ''));
                $optionHtml = '<option value="">Select...</option>';
                foreach ($options as $option) {
                    $optionHtml .= "<option value=\"{$option}\">{$option}</option>";
                }
                return <<<HTML
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {$field['label']} {$requiredMark}
                        </label>
                        <select name="{$field['name']}" {$required}
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            {$optionHtml}
                        </select>
                    </div>
                HTML;
                
            case 'checkbox':
                return <<<HTML
                    <div class="flex items-start">
                        <input type="checkbox" name="{$field['name']}" id="{$field['name']}" {$required}
                            class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="{$field['name']}" class="ml-3 text-sm text-gray-700">
                            {$field['label']} {$requiredMark}
                        </label>
                    </div>
                HTML;
                
            case 'radio':
                $options = array_map('trim', explode(',', $field['options'] ?? ''));
                $html = "<div><label class=\"block text-sm font-medium text-gray-700 mb-2\">{$field['label']} {$requiredMark}</label>";
                foreach ($options as $option) {
                    $html .= <<<HTML
                        <div class="flex items-center">
                            <input type="radio" name="{$field['name']}" value="{$option}" id="{$field['name']}_{$option}" {$required}
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <label for="{$field['name']}_{$option}" class="ml-3 text-sm text-gray-700">{$option}</label>
                        </div>
                    HTML;
                }
                $html .= '</div>';
                return $html;
                
            case 'file':
                return <<<HTML
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {$field['label']} {$requiredMark}
                        </label>
                        <input type="file" name="{$field['name']}" {$required}
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                HTML;
                
            default: // text, email, number, date
                $type = $field['type'];
                return <<<HTML
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {$field['label']} {$requiredMark}
                        </label>
                        <input type="{$type}" name="{$field['name']}" value="" {$required}
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="{$field['placeholder']}">
                    </div>
                HTML;
        }
    }
}
