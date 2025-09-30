<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Alpine.js</title>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-md mx-auto">
        <h1 class="text-2xl font-bold mb-4">Alpine.js Debug</h1>
        
        <!-- Debug Info -->
        <div class="bg-yellow-100 p-4 rounded mb-4">
            <p><strong>Alpine.js Status:</strong> <span id="alpine-status">Checking...</span></p>
            <p><strong>Window.Alpine:</strong> <span id="window-alpine">Checking...</span></p>
        </div>
        
        <!-- Test Dropdown -->
        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
            <button @click="open = ! open" class="bg-blue-500 text-white px-4 py-2 rounded">
                Test Dropdown
            </button>
            
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute z-50 mt-2 w-48 bg-white rounded-md shadow-lg"
                 style="display: none;">
                <div class="py-1">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                </div>
            </div>
        </div>
        
        <!-- Console Test -->
        <div class="mt-4">
            <button onclick="testAlpine()" class="bg-green-500 text-white px-4 py-2 rounded">
                Test Alpine.js
            </button>
        </div>
        
        <div id="result" class="mt-4 p-4 bg-gray-200 rounded"></div>
    </div>

    <script>
        function testAlpine() {
            const result = document.getElementById('result');
            
            // Check Alpine.js
            if (window.Alpine) {
                result.innerHTML = '<p class="text-green-600">✅ Alpine.js is loaded!</p>';
            } else {
                result.innerHTML = '<p class="text-red-600">❌ Alpine.js is NOT loaded!</p>';
            }
        }
        
        // Check on page load
        document.addEventListener('DOMContentLoaded', function() {
            const alpineStatus = document.getElementById('alpine-status');
            const windowAlpine = document.getElementById('window-alpine');
            
            if (window.Alpine) {
                alpineStatus.textContent = '✅ Loaded';
                alpineStatus.className = 'text-green-600';
            } else {
                alpineStatus.textContent = '❌ Not loaded';
                alpineStatus.className = 'text-red-600';
            }
            
            windowAlpine.textContent = window.Alpine ? '✅ Available' : '❌ Not available';
        });
    </script>
</body>
</html>
