<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Media source playbook · Treep</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

@php
    $categories = [
        [
            'title' => 'Photography',
            'use' => 'Hero banners, gallery stacks, avatars',
            'sources' => [
                ['name' => 'Unsplash', 'url' => 'https://unsplash.com', 'note' => 'Hero-grade photography; free with optional attribution.'],
                ['name' => 'Pexels', 'url' => 'https://pexels.com', 'note' => 'City and travel-heavy catalog; free commercial use.'],
                ['name' => 'Pixabay', 'url' => 'https://pixabay.com', 'note' => 'Photos + vectors; filter for CC0/Free license items.'],
                ['name' => 'Reshot', 'url' => 'https://reshot.com', 'note' => 'Maker-friendly curation; commercial use without attribution.'],
            ],
        ],
        [
            'title' => 'Illustrations & SVG icon sets',
            'use' => 'Onboarding, empty states, UI chrome',
            'sources' => [
                ['name' => 'unDraw', 'url' => 'https://undraw.co/illustrations', 'note' => 'Color-tunable SVGs; no attribution.'],
                ['name' => 'Open Doodles', 'url' => 'https://www.opendoodles.com', 'note' => 'Playful CC0 illustrations.'],
                ['name' => 'Heroicons', 'url' => 'https://heroicons.com', 'note' => 'MIT-licensed outline and solid icons.'],
                ['name' => 'Lucide', 'url' => 'https://lucide.dev', 'note' => 'Feather-style line icons; ISC license.'],
                ['name' => 'Phosphor Icons', 'url' => 'https://phosphoricons.com', 'note' => 'Duotone + filled sets; MIT.'],
            ],
        ],
        [
            'title' => 'Maps, geography, and flags',
            'use' => 'World canvas, city callouts, flag pills',
            'sources' => [
                ['name' => 'Wikimedia Commons', 'url' => 'https://commons.wikimedia.org/wiki/Category:SVG_maps', 'note' => 'Vector base maps; respect CC BY-SA attribution.'],
                ['name' => 'Natural Earth', 'url' => 'https://www.naturalearthdata.com', 'note' => 'Public-domain map data for custom SVG/PNG layers.'],
                ['name' => 'FlagCDN', 'url' => 'https://flagcdn.com', 'note' => 'Consistent SVG/PNG country flags.'],
            ],
        ],
        [
            'title' => 'Patterns, gradients, and backgrounds',
            'use' => 'Section dividers, cards, hero texture',
            'sources' => [
                ['name' => 'Haikei', 'url' => 'https://haikei.app', 'note' => 'Organic SVG blobs, waves, and gradients.'],
                ['name' => 'SVGBackgrounds', 'url' => 'https://www.svgbackgrounds.com', 'note' => 'Repeating SVG patterns under permissive license.'],
                ['name' => 'HeroPatterns', 'url' => 'https://heropatterns.com', 'note' => 'Tailwind-ready SVG textures.'],
                ['name' => 'uiGradients', 'url' => 'https://uigradients.com', 'note' => 'Curated gradient palettes for covers.'],
            ],
        ],
        [
            'title' => 'Video and motion',
            'use' => 'Hero loops, timeline bumpers',
            'sources' => [
                ['name' => 'Coverr', 'url' => 'https://coverr.co', 'note' => 'Attribution-free loops ready for headers.'],
                ['name' => 'Pexels Video', 'url' => 'https://www.pexels.com/video', 'note' => 'Travel clips under Pexels license.'],
                ['name' => 'Mixkit', 'url' => 'https://mixkit.co/free-stock-video/', 'note' => 'Cinematic scenes and transitions.'],
            ],
        ],
        [
            'title' => 'Audio (ambience + UI)',
            'use' => 'Ambient beds, chimes, and foley',
            'sources' => [
                ['name' => 'Freesound', 'url' => 'https://freesound.org', 'note' => 'Filter for CC0 to avoid attribution.'],
                ['name' => 'Mixkit Sounds', 'url' => 'https://mixkit.co/free-sound-effects/', 'note' => 'Royalty-free UI and ambience.'],
                ['name' => 'Bensound', 'url' => 'https://www.bensound.com', 'note' => 'Background music; attribution needed on free tier.'],
            ],
        ],
    ];
    $governance = [
        'Store canonical URLs, license, and attribution in MarketingAsset records.',
        'Prefer SVG for icons/logos; compress hero JPGs to ~200KB to keep Lighthouse happy.',
        'Add footer or modal attribution for CC BY / CC BY-SA assets.',
        'Re-run "php artisan db:seed --class=MarketingAssetSeeder" after adding new marketing assets.',
    ];
@endphp

<body class="bg-gradient-to-br from-indigo-50 via-white to-sky-50 font-sans text-gray-900">
    <div class="mx-auto flex min-h-screen max-w-6xl flex-col gap-10 px-6 py-10 lg:px-10">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500">Treep media kit</p>
                <h1 class="text-3xl font-semibold text-gray-900">Media source playbook</h1>
                <p class="mt-2 max-w-3xl text-sm text-gray-600">Vetted sources for SVGs, photography, maps, patterns, video, and audio so the marketing site stays populated with gorgeous, compliant visuals.</p>
            </div>
            <a href="{{ url('/') }}" class="rounded-xl bg-white/80 px-4 py-2 text-sm font-semibold text-indigo-700 shadow ring-1 ring-white/60 hover:text-indigo-800" wire:navigate>Back to home</a>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            @foreach ($categories as $category)
                <div class="rounded-2xl bg-white/90 p-5 shadow ring-1 ring-white/70">
                    <p class="text-xs uppercase tracking-wide text-gray-500">{{ $category['use'] }}</p>
                    <h2 class="mt-1 text-xl font-semibold text-gray-900">{{ $category['title'] }}</h2>
                    <ul class="mt-3 space-y-2 text-sm text-gray-700">
                        @foreach ($category['sources'] as $source)
                            <li class="flex gap-2">
                                <span class="mt-1 h-1.5 w-1.5 rounded-full bg-indigo-500"></span>
                                <div>
                                    <a href="{{ $source['url'] }}" target="_blank" rel="noreferrer" class="font-semibold text-indigo-600 hover:text-indigo-700">{{ $source['name'] }}</a>
                                    <p class="text-xs text-gray-600">{{ $source['note'] }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

        <div class="rounded-2xl bg-white/90 p-5 shadow ring-1 ring-white/70">
            <p class="text-xs uppercase tracking-wide text-gray-500">Attribution & governance</p>
            <h2 class="mt-1 text-xl font-semibold text-gray-900">Keep licensing clean</h2>
            <ul class="mt-3 grid gap-2 text-sm text-gray-700 sm:grid-cols-2">
                @foreach ($governance as $tip)
                    <li class="flex items-start gap-2">
                        <span class="mt-1 h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        <span>{{ $tip }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</body>

</html>
