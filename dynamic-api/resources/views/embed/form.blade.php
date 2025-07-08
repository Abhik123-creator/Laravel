<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $form->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Remove default margins for iframe embedding */
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        /* Ensure proper height calculation for iframe */
        html, body {
            height: auto;
            min-height: 100%;
        }
    </style>
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
<body class="bg-white">
    <div class="p-4 max-w-2xl mx-auto">
        <!-- Compact Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $form->name }}</h2>
            @if($form->description)
                <p class="text-gray-600">{{ $form->description }}</p>
            @endif
        </div>

        @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-3">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-4 w-4 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-2">
                        <h3 class="text-sm font-medium text-red-800">Please correct the errors:</h3>
                        <div class="mt-1 text-sm text-red-700">
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

        <form method="POST" action="{{ route('embed.form.submit', $form->slug) }}" class="space-y-4" id="embed-form">
            @csrf
            
            @foreach($form->fields as $field)
                <div>
                    <label for="{{ $field->name }}" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ $field->label ?: ucfirst($field->name) }}
                        @if($field->required ?? true)
                            <span class="text-red-500">*</span>
                        @endif
                    </label>
                    
                    @if($field->description)
                        <p class="text-xs text-gray-500 mb-2">{{ $field->description }}</p>
                    @endif
                    
                    @switch($field->type)
                        @case('text')
                            <textarea 
                                id="{{ $field->name }}" 
                                name="{{ $field->name }}" 
                                rows="3" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error($field->name) border-red-300 @enderror"
                                placeholder="Enter {{ strtolower($field->label ?: $field->name) }}"
                            >{{ old($field->name) }}</textarea>
                            @break
                        
                        @case('email')
                            <input 
                                type="email" 
                                id="{{ $field->name }}" 
                                name="{{ $field->name }}" 
                                value="{{ old($field->name) }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error($field->name) border-red-300 @enderror"
                                placeholder="Enter {{ strtolower($field->label ?: $field->name) }}"
                            >
                            @break
                        
                        @case('integer')
                            <input 
                                type="number" 
                                id="{{ $field->name }}" 
                                name="{{ $field->name }}" 
                                value="{{ old($field->name) }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error($field->name) border-red-300 @enderror"
                                placeholder="Enter {{ strtolower($field->label ?: $field->name) }}"
                            >
                            @break
                        
                        @case('date')
                            <input 
                                type="date" 
                                id="{{ $field->name }}" 
                                name="{{ $field->name }}" 
                                value="{{ old($field->name) }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error($field->name) border-red-300 @enderror"
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
                        
                        @case('radio')
                            <div class="space-y-2">
                                @if($field->options && is_array($field->options))
                                    @foreach($field->options as $option)
                                        <div class="flex items-center">
                                            <input 
                                                type="radio" 
                                                id="{{ $field->name }}_{{ $loop->index }}" 
                                                name="{{ $field->name }}" 
                                                value="{{ $option['value'] }}"
                                                {{ old($field->name) == $option['value'] ? 'checked' : '' }}
                                                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 @error($field->name) border-red-300 @enderror"
                                            >
                                            <label for="{{ $field->name }}_{{ $loop->index }}" class="ml-3 text-sm text-gray-700">
                                                {{ $option['label'] }}
                                            </label>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            @break
                        
                        @case('checkbox')
                            <div class="space-y-2">
                                @if($field->options && is_array($field->options))
                                    @foreach($field->options as $option)
                                        <div class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                id="{{ $field->name }}_{{ $loop->index }}" 
                                                name="{{ $field->name }}[]" 
                                                value="{{ $option['value'] }}"
                                                {{ is_array(old($field->name)) && in_array($option['value'], old($field->name)) ? 'checked' : '' }}
                                                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded @error($field->name) border-red-300 @enderror"
                                            >
                                            <label for="{{ $field->name }}_{{ $loop->index }}" class="ml-3 text-sm text-gray-700">
                                                {{ $option['label'] }}
                                            </label>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            @break
                        
                        @case('select')
                            <select 
                                id="{{ $field->name }}" 
                                name="{{ $field->name }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error($field->name) border-red-300 @enderror"
                            >
                                <option value="">Choose {{ strtolower($field->label ?: $field->name) }}</option>
                                @if($field->options && is_array($field->options))
                                    @foreach($field->options as $option)
                                        <option 
                                            value="{{ $option['value'] }}"
                                            {{ old($field->name) == $option['value'] ? 'selected' : '' }}
                                        >
                                            {{ $option['label'] }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @break
                        
                        @default
                            <input 
                                type="text" 
                                id="{{ $field->name }}" 
                                name="{{ $field->name }}" 
                                value="{{ old($field->name) }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error($field->name) border-red-300 @enderror"
                                placeholder="Enter {{ strtolower($field->label ?: $field->name) }}"
                            >
                    @endswitch
                    
                    @error($field->name)
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach

            @if($form->captcha_enabled && $captcha)
                <div class="border-t border-gray-200 pt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Security Verification <span class="text-red-500">*</span>
                    </label>
                    
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="flex-1">
                                <img 
                                    src="{{ $captcha['image'] }}" 
                                    alt="Captcha" 
                                    class="border border-gray-300 rounded bg-white max-w-full h-auto"
                                    id="captcha-image"
                                >
                            </div>
                            <button 
                                type="button" 
                                onclick="refreshCaptcha()" 
                                class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50"
                                title="Get a new captcha"
                            >
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <input type="hidden" name="captcha_id" value="{{ $captcha['id'] }}" id="captcha-id">
                    <input 
                        type="number" 
                        id="captcha_answer" 
                        name="captcha_answer" 
                        value="{{ old('captcha_answer') }}"
                        class="block w-full max-w-xs rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm @error('captcha_answer') border-red-300 @enderror"
                        placeholder="Enter your answer"
                        autocomplete="off"
                    >
                    
                    @error('captcha_answer')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <div class="pt-4 border-t border-gray-200">
                <button 
                    type="submit" 
                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200"
                    id="submit-btn"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Submit Response
                </button>
            </div>
        </form>
    </div>

    <script>
        // Auto-resize iframe when content changes
        function resizeIframe() {
            const height = Math.max(document.body.scrollHeight, document.body.offsetHeight);
            window.parent.postMessage({
                type: 'resize',
                height: height + 50 // Add some padding
            }, '*');
        }

        // Resize on load and form changes
        document.addEventListener('DOMContentLoaded', function() {
            resizeIframe();
            
            // Resize when form elements change
            const form = document.getElementById('embed-form');
            form.addEventListener('input', resizeIframe);
            form.addEventListener('change', resizeIframe);
            
            // Submit button loading state
            const submitBtn = document.getElementById('submit-btn');
            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
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
            
            button.disabled = true;
            button.innerHTML = '<svg class="animate-spin h-3 w-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            
            try {
                const response = await fetch('{{ route("captcha.refresh") }}', {
                    method: 'GET',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const data = await response.json();
                if (data.error) throw new Error(data.error);
                
                document.getElementById('captcha-image').src = data.image;
                document.getElementById('captcha-id').value = data.id;
                document.getElementById('captcha_answer').value = '';
                
                button.innerHTML = '<svg class="h-3 w-3 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>';
                setTimeout(() => {
                    button.innerHTML = originalContent;
                    button.disabled = false;
                    resizeIframe();
                }, 1000);
                
            } catch (error) {
                console.error('Failed to refresh captcha:', error);
                button.innerHTML = originalContent;
                button.disabled = false;
            }
        }

        // Listen for resize requests from parent
        window.addEventListener('load', resizeIframe);
        window.addEventListener('resize', resizeIframe);
    </script>
</body>
</html>
