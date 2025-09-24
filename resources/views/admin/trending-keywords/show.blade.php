@extends('layouts.admin')

@section('title', 'Dettaglio: ' . $keyword)

@section('content')
<!-- Vue.js Trends Detail App -->
<div id="trends-detail-app"
     data-keyword="{{ $keyword }}"
     data-initial-data="{{ json_encode([
         'keyword' => $keyword,
         'keywordStats' => $keywordStats,
         'trendsData' => $trendsData,
         'regionPerformance' => $regionPerformance,
         'timeSeriesData' => $timeSeriesData,
         'availableRegions' => $availableRegions,
         'recentTrends' => $recentTrends
     ]) }}">
</div>
@endsection
