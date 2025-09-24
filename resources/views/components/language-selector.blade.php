<!-- Language Selector Component -->
<div class="relative inline-block text-left">
    <div>
        <button type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                id="language-menu" aria-expanded="true" aria-haspopup="true"
                onclick="toggleLanguageMenu()">
            @if(app()->getLocale() === 'it')
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect width="20" height="14" x="2" y="5" rx="2" ry="2"/>
                    <rect width="6" height="14" x="2" y="5" fill="#009246"/>
                    <rect width="6" height="14" x="9" y="5" fill="#fff"/>
                    <rect width="6" height="14" x="15" y="5" fill="#ce2b37"/>
                </svg>
                {{ __('common.italian') }}
            @else
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect width="20" height="14" x="2" y="5" rx="2" ry="2"/>
                    <rect width="20" height="2" x="2" y="5" fill="#012169"/>
                    <rect width="20" height="2" x="2" y="7" fill="#fff"/>
                    <rect width="20" height="2" x="2" y="9" fill="#ce1124"/>
                    <rect width="20" height="2" x="2" y="11" fill="#fff"/>
                    <rect width="20" height="2" x="2" y="13" fill="#012169"/>
                    <rect width="20" height="2" x="2" y="15" fill="#fff"/>
                    <rect width="20" height="2" x="2" y="17" fill="#ce1124"/>
                </svg>
                {{ __('common.english') }}
            @endif
            <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>

    <div id="language-dropdown" class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50" role="menu" aria-orientation="vertical" aria-labelledby="language-menu">
        <div class="py-1" role="none">
            <a href="{{ route('language.switch', 'it') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() === 'it' ? 'bg-gray-50 font-semibold' : '' }}" role="menuitem">
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect width="20" height="14" x="2" y="5" rx="2" ry="2"/>
                    <rect width="6" height="14" x="2" y="5" fill="#009246"/>
                    <rect width="6" height="14" x="9" y="5" fill="#fff"/>
                    <rect width="6" height="14" x="15" y="5" fill="#ce2b37"/>
                </svg>
                {{ __('common.italian') }}
                @if(app()->getLocale() === 'it')
                    <svg class="ml-auto w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                @endif
            </a>
            <a href="{{ route('language.switch', 'en') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() === 'en' ? 'bg-gray-50 font-semibold' : '' }}" role="menuitem">
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect width="20" height="14" x="2" y="5" rx="2" ry="2"/>
                    <rect width="20" height="2" x="2" y="5" fill="#012169"/>
                    <rect width="20" height="2" x="2" y="7" fill="#fff"/>
                    <rect width="20" height="2" x="2" y="9" fill="#ce1124"/>
                    <rect width="20" height="2" x="2" y="11" fill="#fff"/>
                    <rect width="20" height="2" x="2" y="13" fill="#012169"/>
                    <rect width="20" height="2" x="2" y="15" fill="#fff"/>
                    <rect width="20" height="2" x="2" y="17" fill="#ce1124"/>
                </svg>
                {{ __('common.english') }}
                @if(app()->getLocale() === 'en')
                    <svg class="ml-auto w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                @endif
            </a>
        </div>
    </div>
</div>

<script>
function toggleLanguageMenu() {
    const dropdown = document.getElementById('language-dropdown');
    dropdown.classList.toggle('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const menu = document.getElementById('language-menu');
    const dropdown = document.getElementById('language-dropdown');

    if (!menu.contains(event.target) && !dropdown.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
});
</script>
