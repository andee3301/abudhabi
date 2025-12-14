@extends('errors::minimal')

@section('title', __('Server Error'))
@section('code', '500')
@section('message')
    <div class="space-y-2 text-left">
        <p class="text-sm text-gray-700">Something went wrong while loading this page.</p>
        <p class="text-xs text-gray-500">
            We've logged the error (Sentry/Slack). Please retry in a moment or contact support with the timestamp {{ now()->toDateTimeString() }}.
        </p>
    </div>
@endsection
