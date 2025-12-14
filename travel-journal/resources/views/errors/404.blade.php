@extends('errors::minimal')

@section('title', __('Not Found'))
@section('code', '404')
@section('message')
    <div class="space-y-2 text-left">
        <p class="text-sm text-gray-700">We couldn't find that page. The link may be broken or the trip was archived.</p>
        <p class="text-xs text-gray-500">If you think this is a mistake, share the URL with the team or file a bug (see CONTRIBUTING).</p>
    </div>
@endsection
