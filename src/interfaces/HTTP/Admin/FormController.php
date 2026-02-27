<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Admin;

use Application\Services\FormAppService;
use Infrastructure\Persistence\DatabaseConnection;
use Infrastructure\Persistence\UnitOfWork;
use Infrastructure\Persistence\Repositories\FormRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Admin Form Controller - Form Builder
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

    public function index(): Response
    {
        $forms = $this->formService->listAll();
        
        $rows = '';
        foreach ($forms as $form) {
            $schema = json_decode($form->schema(), true);
            $fieldsCount = count($schema ?? []);
            
            $rows .= <<<TR
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-4">
                        <a href="/admin/forms/{$form->id()}/edit" class="text-blue-600 hover:underline font-medium">
                            {$form->name()}
                        </a>
                    </td>
                    <td class="p-4 text-gray-500">/{$form->slug()}</td>
                    <td class="p-4 text-gray-500">{$fieldsCount} fields</td>
                    <td class="p-4 text-gray-500">{$form->createdAt()}</td>
                    <td class="p-4">
                        <div class="flex gap-2">
                            <a href="/admin/forms/{$form->id()}/edit" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                                Edit
                            </a>
                            <a href="/admin/forms/{$form->id()}/submissions" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
                                Submissions
                            </a>
                            <a href="/form/{$form->slug()}" target="_blank" class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700 text-sm">
                                View
                            </a>
                        </div>
                    </td>
                </tr>
            TR;
        }

        $html = $this->layout(<<<CONTENT
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Forms</h2>
                <a href="/admin/forms/create" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Form
                </a>
            </div>
            
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-4 text-left text-sm font-medium text-gray-500">Name</th>
                            <th class="p-4 text-left text-sm font-medium text-gray-500">Slug</th>
                            <th class="p-4 text-left text-sm font-medium text-gray-500">Fields</th>
                            <th class="p-4 text-left text-sm font-medium text-gray-500">Created</th>
                            <th class="p-4 text-left text-sm font-medium text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$rows}
                    </tbody>
                </table>
            </div>
        CONTENT);

        return new Response($html);
    }

    public function create(): Response
    {
        $html = $this->layout($this->formBuilderTemplate(null));
        return new Response($html);
    }

    public function store(Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            $form = $this->formService->create(
                name: $data['name'],
                schema: $data['schema'] ?? [],
                emailNotification: $data['email_notification'] ?? null,
                successMessage: $data['success_message'] ?? 'Thank you for your submission!',
            );

            return new Response(json_encode(['success' => true, 'id' => $form->id()]), 200, ['Content-Type' => 'application/json']);
        } catch (\Throwable $e) {
            return new Response(json_encode(['error' => $e->getMessage()]), 500, ['Content-Type' => 'application/json']);
        }
    }

    public function edit(string $id): Response
    {
        $form = $this->formService->findById($id);
        
        if (!$form) {
            return new Response('Form not found', 404);
        }

        $html = $this->layout($this->formBuilderTemplate($form));
        return new Response($html);
    }

    public function update(string $id, Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            $this->formService->update(
                formId: $id,
                name: $data['name'] ?? null,
                schema: $data['schema'] ?? null,
                emailNotification: $data['email_notification'] ?? null,
                successMessage: $data['success_message'] ?? null,
            );

            return new Response(json_encode(['success' => true]), 200, ['Content-Type' => 'application/json']);
        } catch (\Throwable $e) {
            return new Response(json_encode(['error' => $e->getMessage()]), 500, ['Content-Type' => 'application/json']);
        }
    }

    public function submissions(string $id): Response
    {
        $form = $this->formService->findById($id);
        
        if (!$form) {
            return new Response('Form not found', 404);
        }

        $db = new DatabaseConnection();
        $uow = new UnitOfWork($db);
        $submissionRepo = new \Infrastructure\Persistence\Repositories\FormSubmissionRepository($uow);
        $submissions = $submissionRepo->findByFormId($id);

        $rows = '';
        foreach ($submissions as $submission) {
            $data = json_encode($submission->data(), JSON_PRETTY_PRINT);
            $submittedAt = $submission->submittedAt();
            
            $rows .= <<<TR
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-4 text-sm text-gray-500">{$submittedAt}</td>
                    <td class="p-4">
                        <details class="group">
                            <summary class="cursor-pointer text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View Submission
                            </summary>
                            <pre class="mt-2 p-3 bg-gray-50 rounded-lg text-xs overflow-auto max-h-64">{$data}</pre>
                        </details>
                    </td>
                    <td class="p-4 text-sm text-gray-500">{$submission->ipAddress()}</td>
                </tr>
            TR;
        }

        $schema = json_decode($form->schema(), true);
        $fieldsList = '';
        foreach ($schema as $field) {
            $fieldsList .= "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800\">{$field['label']}</span>";
        }

        $html = $this->layout(<<<CONTENT
            <div class="max-w-6xl mx-auto">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Form Submissions</h2>
                        <p class="text-gray-600 mt-1">{$form->name()} - {$form->slug()}</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="/admin/forms/{$id}/edit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Edit Form
                        </a>
                        <a href="/admin/forms" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                            ← Back to Forms
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Form Fields</h3>
                    <div class="flex flex-wrap gap-2">
                        {$fieldsList}
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b">
                        <h3 class="text-lg font-semibold text-gray-800">Submissions (" . count($submissions) . ")</h3>
                    </div>
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-4 text-left text-sm font-medium text-gray-500">Submitted</th>
                                <th class="p-4 text-left text-sm font-medium text-gray-500">Data</th>
                                <th class="p-4 text-left text-sm font-medium text-gray-500">IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$rows}
                        </tbody>
                    </table>
                </div>
            </div>
        CONTENT);

        return new Response($html);
    }

    private function formBuilderTemplate(?object $form): string
    {
        $formName = $form !== null ? $form->name() : '';
        $formSlug = $form !== null ? $form->slug() : '';
        $formSchema = $form !== null ? json_encode($form->schema()) : '[]';
        $emailNotification = $form !== null ? ($form->emailNotification() ?? '') : '';
        $successMessage = $form !== null ? ($form->successMessage() ?? 'Thank you for your submission!') : 'Thank you for your submission!';
        $isEdit = $form !== null;
        $pageTitle = $isEdit ? 'Edit Form: ' . $formName : 'Create New Form';
        $saveButtonText = $isEdit ? 'Update Form' : 'Create Form';
        $formIdPath = $isEdit ? '/' . $form->id() : '';
        $httpMethod = $isEdit ? 'PUT' : 'POST';

        return <<<HTML
            <div class="max-w-7xl mx-auto">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">
                        {$pageTitle}
                    </h2>
                    <a href="/admin/forms" class="text-gray-600 hover:text-gray-800">← Back to Forms</a>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Form Builder - Field Palette -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Form Fields</h3>
                            <p class="text-sm text-gray-600 mb-4">Click to add fields to your form</p>
                            
                            <div class="space-y-2" id="fieldPalette">
                                <button type="button" onclick="addField('text')" class="w-full p-3 bg-gray-50 hover:bg-blue-50 border border-gray-200 rounded-lg text-left flex items-center gap-3 transition">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                                    </svg>
                                    <span>Text Input</span>
                                </button>
                                <button type="button" onclick="addField('email')" class="w-full p-3 bg-gray-50 hover:bg-blue-50 border border-gray-200 rounded-lg text-left flex items-center gap-3 transition">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Email</span>
                                </button>
                                <button type="button" onclick="addField('textarea')" class="w-full p-3 bg-gray-50 hover:bg-blue-50 border border-gray-200 rounded-lg text-left flex items-center gap-3 transition">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                    </svg>
                                    <span>Textarea</span>
                                </button>
                                <button type="button" onclick="addField('select')" class="w-full p-3 bg-gray-50 hover:bg-blue-50 border border-gray-200 rounded-lg text-left flex items-center gap-3 transition">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                    <span>Dropdown</span>
                                </button>
                                <button type="button" onclick="addField('checkbox')" class="w-full p-3 bg-gray-50 hover:bg-blue-50 border border-gray-200 rounded-lg text-left flex items-center gap-3 transition">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Checkbox</span>
                                </button>
                                <button type="button" onclick="addField('radio')" class="w-full p-3 bg-gray-50 hover:bg-blue-50 border border-gray-200 rounded-lg text-left flex items-center gap-3 transition">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    <span>Radio</span>
                                </button>
                                <button type="button" onclick="addField('number')" class="w-full p-3 bg-gray-50 hover:bg-blue-50 border border-gray-200 rounded-lg text-left flex items-center gap-3 transition">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                    </svg>
                                    <span>Number</span>
                                </button>
                                <button type="button" onclick="addField('date')" class="w-full p-3 bg-gray-50 hover:bg-blue-50 border border-gray-200 rounded-lg text-left flex items-center gap-3 transition">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Date</span>
                                </button>
                                <button type="button" onclick="addField('file')" class="w-full p-3 bg-gray-50 hover:bg-blue-50 border border-gray-200 rounded-lg text-left flex items-center gap-3 transition">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    <span>File Upload</span>
                                </button>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow p-6 mt-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Form Settings</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Form Name *</label>
                                    <input type="text" id="formName" value="{$formName}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Contact Form">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Notification</label>
                                    <input type="email" id="emailNotification" value="{$emailNotification}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="admin@example.com">
                                    <p class="text-xs text-gray-500 mt-1">Send submissions to this email</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Success Message</label>
                                    <textarea id="successMessage" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{$successMessage}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Canvas -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-800">Form Preview</h3>
                                <button type="button" onclick="clearFields()" class="text-sm text-red-600 hover:text-red-800">Clear All</button>
                            </div>
                            
                            <div id="formCanvas" class="space-y-4 min-h-[400px] p-4 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                <!-- Fields will be added here -->
                            </div>
                            
                            <div class="mt-6 flex gap-4">
                                <button type="button" onclick="saveForm()" id="saveBtn" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                    </svg>
                                    {$saveButtonText}
                                </button>
                                <span id="saveStatus" class="hidden px-4 py-2 rounded-lg"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                let formFields = {$formSchema};
                
                function renderFields() {
                    const canvas = document.getElementById('formCanvas');
                    canvas.innerHTML = formFields.map((field, index) => renderField(field, index)).join('');
                }
                
                function renderField(field, index) {
                    return \`
                        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm group relative" data-index="\${index}">
                            <button type="button" onclick="removeField(\${index})" class="absolute top-2 right-2 text-red-500 hover:text-red-700 opacity-0 group-hover:opacity-100 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Label</label>
                                    <input type="text" value="\${field.label}" onchange="updateField(\${index}, 'label', this.value)" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Field Label">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Name (variable)</label>
                                    <input type="text" value="\${field.name}" onchange="updateField(\${index}, 'name', this.value)" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="field_name">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Placeholder</label>
                                    <input type="text" value="\${field.placeholder || ''}" onchange="updateField(\${index}, 'placeholder', this.value)" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Enter placeholder">
                                </div>
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-2 mt-6">
                                        <input type="checkbox" \${field.required ? 'checked' : ''} onchange="updateField(\${index}, 'required', this.checked)" 
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm text-gray-700">Required</span>
                                    </label>
                                    \${field.type === 'select' || field.type === 'radio' ? \`
                                        <input type="text" value="\${field.options || ''}" onchange="updateField(\${index}, 'options', this.value)" 
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Option1,Option2,Option3">
                                    \` : ''}
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    \${field.type}
                                </span>
                            </div>
                        </div>
                    \`;
                }
                
                function addField(type) {
                    const labels = {
                        text: 'Text Field', email: 'Email', textarea: 'Message',
                        select: 'Select Option', checkbox: 'Checkbox', radio: 'Radio',
                        number: 'Number', date: 'Date', file: 'File Upload'
                    };
                    
                    formFields.push({
                        type, label: labels[type], name: type + '_' + Date.now(),
                        placeholder: '', required: false, options: ''
                    });
                    renderFields();
                }
                
                function updateField(index, key, value) {
                    formFields[index][key] = value;
                }
                
                function removeField(index) {
                    formFields.splice(index, 1);
                    renderFields();
                }
                
                function clearFields() {
                    if (confirm('Remove all fields?')) {
                        formFields = [];
                        renderFields();
                    }
                }
                
                async function saveForm() {
                    const name = document.getElementById('formName').value;
                    const emailNotification = document.getElementById('emailNotification').value;
                    const successMessage = document.getElementById('successMessage').value;
                    
                    if (!name) {
                        alert('Please enter a form name');
                        return;
                    }
                    
                    if (formFields.length === 0) {
                        alert('Please add at least one field');
                        return;
                    }
                    
                    const btn = document.getElementById('saveBtn');
                    const status = document.getElementById('saveStatus');
                    btn.disabled = true;
                    btn.innerHTML = 'Saving...';
                    
                    try {
                        const response = await fetch('/admin/forms{$formIdPath}', {
                            method: '{$httpMethod}',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify({
                                name, schema: formFields, email_notification: emailNotification,
                                success_message: successMessage
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            status.className = 'px-4 py-2 rounded-lg bg-green-100 text-green-700';
                            status.textContent = 'Form saved successfully!';
                            status.classList.remove('hidden');
                            setTimeout(() => window.location.href = '/admin/forms', 1500);
                        } else {
                            throw new Error(result.error);
                        }
                    } catch (error) {
                        status.className = 'px-4 py-2 rounded-lg bg-red-100 text-red-700';
                        status.textContent = 'Error: ' + error.message;
                        status.classList.remove('hidden');
                    } finally {
                        btn.disabled = false;
                        btn.innerHTML = '{$saveButtonText}';
                    }
                }
                
                // Initialize
                renderFields();
            </script>
        HTML;
    }

    private function layout(string $content): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Builder - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <div class="w-64 bg-gray-800 text-white">
            <div class="p-4"><h1 class="text-2xl font-bold">Admin Panel</h1></div>
            <nav class="mt-4">
                <a href="/admin" class="block px-4 py-2 hover:bg-gray-700">Dashboard</a>
                <a href="/admin/articles" class="block px-4 py-2 hover:bg-gray-700">Articles</a>
                <a href="/admin/pages" class="block px-4 py-2 hover:bg-gray-700">Pages</a>
                <a href="/admin/forms" class="block px-4 py-2 bg-gray-700">Forms</a>
                <a href="/admin/media" class="block px-4 py-2 hover:bg-gray-700">Media</a>
                <a href="/admin/settings" class="block px-4 py-2 hover:bg-gray-700">Settings</a>
            </nav>
        </div>
        <div class="flex-1 overflow-auto p-8">{$content}</div>
    </div>
</body>
</html>
HTML;
    }
}
