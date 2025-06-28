<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-3xl font-bold mb-6 text-gray-800">🔗 Navigation Test</h1>
            <p class="text-gray-600 mb-6">Test all navigation links to ensure they're working correctly</p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($routes as $name => $url)
                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                    <h3 class="font-semibold text-lg mb-2">{{ $name }}</h3>
                    <p class="text-sm text-gray-600 mb-3 break-all">{{ $url }}</p>
                    <div class="flex gap-2">
                        <a href="{{ $url }}" target="_blank"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                            Visit
                        </a>
                        <button onclick="testLink('{{ $url }}', '{{ $name }}')"
                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                            Test
                        </button>
                    </div>
                    <div id="result-{{ $loop->index }}" class="mt-2 text-sm"></div>
                </div>
                @endforeach
            </div>

            <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                <h3 class="font-semibold mb-2">Test Results</h3>
                <div id="testResults" class="space-y-2"></div>
                <button onclick="testAllLinks()" class="mt-4 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">
                    Test All Links
                </button>
            </div>
        </div>
    </div>

    <script>
        async function testLink(url, name) {
            const resultDiv = document.getElementById(`result-${name.replace(/\s+/g, '')}`);
            resultDiv.innerHTML = '<span class="text-yellow-600">Testing...</span>';

            try {
                const response = await fetch(url, { method: 'HEAD' });
                if (response.ok) {
                    resultDiv.innerHTML = '<span class="text-green-600">✅ Working</span>';
                } else {
                    resultDiv.innerHTML = `<span class="text-red-600">❌ Error ${response.status}</span>`;
                }
            } catch (error) {
                resultDiv.innerHTML = '<span class="text-red-600">❌ Failed</span>';
            }
        }

        async function testAllLinks() {
            const routes = @json($routes);
            const resultsDiv = document.getElementById('testResults');
            resultsDiv.innerHTML = '<p class="text-yellow-600">Testing all links...</p>';

            const results = [];
            for (const [name, url] of Object.entries(routes)) {
                try {
                    const response = await fetch(url, { method: 'HEAD' });
                    results.push({
                        name,
                        url,
                        status: response.ok ? 'success' : 'error',
                        code: response.status
                    });
                } catch (error) {
                    results.push({
                        name,
                        url,
                        status: 'failed',
                        error: error.message
                    });
                }
            }

            resultsDiv.innerHTML = results.map(result =>
                `<div class="flex justify-between items-center p-2 border rounded">
                    <span>${result.name}</span>
                    <span class="${result.status === 'success' ? 'text-green-600' : 'text-red-600'}">
                        ${result.status === 'success' ? '✅' : '❌'} ${result.status}
                    </span>
                </div>`
            ).join('');
        }
    </script>
</body>
</html>
