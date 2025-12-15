# Media Source Playbook

Use this cheat sheet to grab on-brand visuals while keeping licensing clean. Every source below is vetted for free commercial use (with attribution notes where required) and works well for hero banners, gallery items, flags, and UI icons.

## Photography (hero, gallery, avatars)
- **Unsplash** — High-quality photography with broad coverage. Free for commercial use, attribution appreciated but not required. Great for hero banners and lifestyle shots.
- **Pexels** — Similar to Unsplash but with stronger travel and city coverage. Free for commercial use, no attribution required.
- **Pixabay** — Large catalog of free photos and vectors; check each item for CC0/Free license and avoid "sponsored" stock links.
- **Reshot** — Curated photos with a maker-centric aesthetic; license permits commercial use without attribution.

## Illustrations & SVG icon sets
- **unDraw** — Continuously updated SVG illustrations with customizable colors; free for commercial use without attribution.
- **Open Doodles / Open Peeps** — Hand-drawn illustration packs with CC0 licensing; ideal for empty states and onboarding.
- **Heroicons** — Outline/solid SVG icons (MIT); pairs well with Tailwind setups.
- **Lucide** — Community fork of Feather icons (ISC license); extensive line icon set for dashboard UI.
- **Phosphor Icons** — Duotone and filled SVGs (MIT); good for marketing callouts.

## Maps, geography, and flags
- **Wikimedia Commons** — Vector world maps and region maps (verify CC BY-SA attribution). Good fallback for neutral map wireframes.
- **Natural Earth** — Public domain map data for generating custom SVG/PNG map layers.
- **FlagCDN** — Consistent, crisp country flags (CC BY-SA 4.0); can be pulled as SVG or PNG via CDN.

## Patterns, gradients, and abstract backgrounds
- **Haikei** — Generates organic SVG blobs, waves, and gradients; exports royalty-free vectors.
- **SVGBackgrounds / HeroPatterns** — Repeating SVG patterns under permissive licenses; useful for subtle backgrounds behind cards.
- **Cool Backgrounds / uiGradients** — Curated gradient palettes for cover art and section dividers.

## Video and motion
- **Coverr** — Short looping background videos with no attribution requirement.
- **Pexels Video** — Free stock clips with the same license as Pexels photos; good for hero headers.
- **Mixkit** — Cinematic clips and simple transitions; free for commercial projects with optional credit.

## Audio (optional ambience)
- **Freesound** — CC-licensed sound effects; filter for CC0 for attribution-free usage.
- **Mixkit Sounds** — Royalty-free UI chimes and ambience.
- **Bensound** — Music tracks for background beds; free tier requires attribution, paid tier removes it.

## Attribution & governance tips
- Store canonical sources and license notes next to the asset record (see `MarketingAssetSeeder`) so future exports stay compliant.
- Prefer SVG for logos, icons, and line art; limit hero/cover JPEGs to ~200KB after compression to keep Lighthouse scores high.
- When using CC BY or CC BY-SA assets, add visible attribution in the footer or the credits modal and keep the source URL handy.
- Re-run `php artisan db:seed --class=MarketingAssetSeeder` after adding new marketing assets so CDN and local fallbacks stay synced.
