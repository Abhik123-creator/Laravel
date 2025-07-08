<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $form->name }} - Dynamic Forms</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $form->name }}</h1>
            @if($form->description)
                <p class="text-lg text-gray-600">{{ $form->description }}</p>
            @endif
            <div class="mt-4 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                Active Form
            </div>
        </div>

        <!-- Form Container -->
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="px-6 py-8">
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('public.form.submit', $form->slug) }}" class="space-y-6">
                    @csrf
                    
                    @foreach($form->fields as $field)
                        <div>
                            <label for="{{ $field->name }}" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ $field->label ?: ucfirst($field->name) }}
                                <span class="text-red-500">*</span>
                            </label>
                            
                            @switch($field->type)
                                @case('text')
                                    <textarea 
                                        id="{{ $field->name }}" 
                                        name="{{ $field->name }}" 
                                        rows="4" 
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error($field->name) border-red-300 @enderror"
                                        placeholder="Enter {{ strtolower($field->label ?: $field->name) }}"
                                    >{{ old($field->name) }}</textarea>
                                    @break
                                
                                @case('email')
                                    <input 
                                        type="email" 
                                        id="{{ $field->name }}" 
                                        name="{{ $field->name }}" 
                                        value="{{ old($field->name) }}"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error($field->name) border-red-300 @enderror"
                                        placeholder="Enter {{ strtolower($field->label ?: $field->name) }}"
                                    >
                                    @break
                                
                                @case('integer')
                                    <input 
                                        type="number" 
                                        id="{{ $field->name }}" 
                                        name="{{ $field->name }}" 
                                        value="{{ old($field->name) }}"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error($field->name) border-red-300 @enderror"
                                        placeholder="Enter {{ strtolower($field->label ?: $field->name) }}"
                                    >
                                    @break
                                
                                @case('date')
                                    <input 
                                        type="date" 
                                        id="{{ $field->name }}" 
                                        name="{{ $field->name }}" 
                                        value="{{ old($field->name) }}"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error($field->name) border-red-300 @enderror"
                                    >
                                    @break
                                
                                @case('boolean')
                                    <div class="flex items-center space-x-3">
                                        <input 
                                            type="hidden" 
                                            name="{{ $field->name }}" 
                                            value="0"
                                        >
                                        <input 
                                            type="checkbox" 
                                            id="{{ $field->name }}" 
                                            name="{{ $field->name }}" 
                                            value="1"
                                            {{ old($field->name) ? 'checked' : '' }}
                                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded @error($field->name) border-red-300 @enderror"
                                        >
                                        <label for="{{ $field->name }}" class="text-sm text-gray-600">
                                            Yes, {{ strtolower($field->label ?: $field->name) }}
                                        </label>
                                    </div>
                                    @break
                                
                                @default
                                    <input 
                                        type="text" 
                                        id="{{ $field->name }}" 
                                        name="{{ $field->name }}" 
                                        value="{{ old($field->name) }}"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error($field->name) border-red-300 @enderror"
                                        placeholder="Enter {{ strtolower($field->label ?: $field->name) }}"
                                    >
                            @endswitch
                            
                            @error($field->name)
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach

                    @if($form->captcha_enabled && $captcha)
                        <div class="border-t border-gray-200 pt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Security Verification
                                <span class="text-red-500">*</span>
                            </label>
                            <p class="text-sm text-gray-600 mb-4">Please solve the math problem below to verify you're human:</p>
                            
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-1">
                                        <img 
                                            src="{{ $captcha['image'] }}" 
                                            alt="Captcha" 
                                            class="border border-gray-300 rounded bg-white"
                                            id="captcha-image"
                                        >
                                    </div>
                                    <button 
                                        type="button" 
                                        onclick="refreshCaptcha()" 
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                                        title="Get a new captcha"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <input 
                                type="hidden" 
                                name="captcha_id" 
                                value="{{ $captcha['id'] }}"
                                id="captcha-id"
                            >
                            <input 
                                type="number" 
                                id="captcha_answer" 
                                name="captcha_answer" 
                                value="{{ old('captcha_answer') }}"
                                class="block w-full max-w-xs rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('captcha_answer') border-red-300 @enderror"
                                placeholder="Enter your answer"
                                autocomplete="off"
                            >
                            
                            @error('captcha_answer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <div class="pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-500">
                                All fields marked with <span class="text-red-500">*</span> are required
                            </p>
                            <button 
                                type="submit" 
                                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Submit Response
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-sm text-gray-500">
            <p>Powered by Dynamic Forms API</p>
            <p class="mt-1">
                Form ID: <code class="bg-gray-100 px-2 py-1 rounded">{{ $form->slug }}</code>
            </p>
        </div>
    </div>

    <script>
        // Add some interactive enhancements
        document.addEventListener('DOMContentLoaded', function() {
            // Focus first input
            const firstInput = document.querySelector('input, textarea, select');
            if (firstInput) {
                firstInput.focus();
            }
            
            // Add loading state to submit button
            const form = document.querySelector('form');
            const submitButton = document.querySelector('button[type="submit"]');
            
            form.addEventListener('submit', function() {
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Submitting...
                `;
            });
        });

        // Refresh captcha function
        async function refreshCaptcha() {
            const button = event.target.closest('button');
            const originalContent = button.innerHTML;
            
            // Show loading state
            button.disabled = true;
            button.innerHTML = '<svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            
            try {
                const response = await fetch('{{ route("captcha.refresh", $form->slug) }}', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.error) {
                    throw new Error(data.error);
                }
                
                // Update captcha image and ID
                document.getElementById('captcha-image').src = data.image;
                document.getElementById('captcha-id').value = data.id;
                document.getElementById('captcha_answer').value = '';
                document.getElementById('captcha_answer').focus();
                
                // Show brief success feedback
                button.innerHTML = '<svg class="h-4 w-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>';
                
                setTimeout(() => {
                    button.innerHTML = originalContent;
                    button.disabled = false;
                }, 1000);
                
            } catch (error) {
                console.error('Failed to refresh captcha:', error);
                
                // Show error state
                button.innerHTML = '<svg class="h-4 w-4 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>';
                
                setTimeout(() => {
                    button.innerHTML = originalContent;
                    button.disabled = false;
                }, 2000);
                
                // Show user-friendly error message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'mt-2 p-2 bg-red-100 border border-red-300 rounded text-sm text-red-700';
                errorDiv.textContent = 'Failed to refresh captcha. Please try again.';
                
                // Remove any existing error message
                const existingError = button.parentNode.querySelector('.mt-2.p-2.bg-red-100');
                if (existingError) {
                    existingError.remove();
                }
                
                button.parentNode.appendChild(errorDiv);
                
                // Remove error message after 3 seconds
                setTimeout(() => {
                    if (errorDiv.parentNode) {
                        errorDiv.remove();
                    }
                }, 3000);
            }
        }
    </script>
</body>
</html>
