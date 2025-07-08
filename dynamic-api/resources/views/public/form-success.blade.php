<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - {{ $form->name }}</title>
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
        <div class="text-center">
            <!-- Success Icon -->
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6">
                <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            
            <!-- Success Message -->
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Thank You!</h1>
            <p class="text-lg text-gray-600 mb-2">Your response has been submitted successfully.</p>
            <p class="text-base text-gray-500 mb-8">We have received your submission for <strong>{{ $form->name }}</strong>.</p>
            
            @if(session('entry_id'))
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8 inline-block">
                    <p class="text-sm text-blue-800">
                        <strong>Reference ID:</strong> 
                        <code class="bg-blue-100 px-2 py-1 rounded text-blue-900">#{{ session('entry_id') }}</code>
                    </p>
                    <p class="text-xs text-blue-600 mt-1">Save this reference ID for your records</p>
                </div>
            @endif
            
            <!-- Action Buttons -->
            <div class="space-y-4 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center">
                <a 
                    href="{{ route('public.form.show', $form->slug) }}" 
                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-primary-300 text-base font-medium rounded-md text-primary-700 bg-white hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Submit Another Response
                </a>
                
                <a 
                    href="{{ url('/') }}" 
                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Go to Homepage
                </a>
            </div>
            
            <!-- Form Info -->
            <div class="mt-12 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Form Details</h3>
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Form Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $form->name }}</dd>
                    </div>
                    @if($form->description)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $form->description }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Submitted At</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ now()->format('M d, Y \a\t g:i A') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Form ID</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <code class="bg-gray-100 px-2 py-1 rounded">{{ $form->slug }}</code>
                        </dd>
                    </div>
                </dl>
            </div>
            
            <!-- Footer -->
            <div class="mt-8 text-center text-sm text-gray-500">
                <p>Powered by Dynamic Forms API</p>
            </div>
        </div>
    </div>
</body>
</html>
