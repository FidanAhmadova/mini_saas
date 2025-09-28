<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Application Error</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <h2 class="text-2xl font-bold text-center mb-6 text-red-600">Application Error</h2>
            
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                <p class="text-red-800">{{ $message ?? 'An error occurred while processing your request.' }}</p>
            </div>
            
            @if(config('app.debug') && isset($exception))
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                    <h3 class="font-semibold text-gray-800 mb-2">Debug Information:</h3>
                    <p class="text-sm text-gray-600 break-all">
                        <strong>Error:</strong> {{ $exception->getMessage() }}<br>
                        <strong>File:</strong> {{ $exception->getFile() }}<br>
                        <strong>Line:</strong> {{ $exception->getLine() }}
                    </p>
                </div>
            @endif
            
            <div class="text-center">
                <a href="{{ url('/') }}" class="text-blue-500 hover:text-blue-700 underline">
                    ← Back to Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>
