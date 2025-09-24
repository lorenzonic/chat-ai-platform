<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Language Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto py-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-bold mb-4">{{ __('common.dashboard') }} - Language Test</h1>

            <div class="mb-4">
                <p><strong>Current Locale:</strong> {{ app()->getLocale() }}</p>
                <p><strong>{{ __('common.welcome') }}</strong></p>
                <p><strong>{{ __('common.login') }}</strong> | <strong>{{ __('common.logout') }}</strong></p>
            </div>

            <div class="space-x-4">
                <a href="{{ route('language.switch', 'it') }}"
                   class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    🇮🇹 Italiano
                </a>
                <a href="{{ route('language.switch', 'en') }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    🇬🇧 English
                </a>
            </div>

            <div class="mt-6 p-4 bg-gray-50 rounded">
                <h3 class="font-bold">Test Translations:</h3>
                <ul class="list-disc ml-6 mt-2">
                    <li>Dashboard: {{ __('common.dashboard') }}</li>
                    <li>Login: {{ __('common.login') }}</li>
                    <li>Logout: {{ __('common.logout') }}</li>
                    <li>Edit: {{ __('common.edit') }}</li>
                    <li>Delete: {{ __('common.delete') }}</li>
                    <li>Active: {{ __('common.active') }}</li>
                    <li>Inactive: {{ __('common.inactive') }}</li>
                </ul>
            </div>

            <div class="mt-6">
                <a href="{{ route('admin.accounts.index') }}"
                   class="text-blue-600 hover:text-blue-800">
                    Go to Admin Panel
                </a>
            </div>
        </div>
    </div>
</body>
</html>
