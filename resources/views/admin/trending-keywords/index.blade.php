@extends('layouts.admin')

@section('title', 'Google Trends Piante')

@section('content')
<div class="mb-6 flex justify-end">
    <form method="POST" action="{{ route('admin.trending-keywords.update-script') }}">
        @csrf
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Aggiorna Trending Keywords
        </button>
    </form>
</div>
<!-- Vue.js Trends Dashboard App -->
<div id="trends-dashboard-app"
     data-initial-data="{{ json_encode([
         'stats' => $stats,
         'trends' => $trends->items(),
         'topKeywords' => $topKeywords,
         'regionStats' => $regionStats,
         'popularKeywords' => $popularKeywords,
         'dailyTrends' => $dailyTrends,
         'availableRegions' => $availableRegions,
         'currentFilters' => [
             'region' => $region,
             'days' => $days,
             'keyword' => $keyword
         ],
         'pagination' => [
             'current_page' => $trends->currentPage(),
             'last_page' => $trends->lastPage(),
             'per_page' => $trends->perPage(),
             'total' => $trends->total()
         ]
     ]) }}">
</div>
@endsection
