# Marketing Asset Sources

TripKit keeps every hero image, flag, and avatar in the `marketing_assets` table so that the welcome page can fall back to local SVGs while optionally pointing at a public CDN source. This document lists the canonical source for each asset along with licensing notes.

| Key | Type | Local Path | CDN / Source | License Notes |
| --- | --- | --- | --- | --- |
| `world_map_wireframe` | Illustration | `public/marketing/world-map.svg` | [Wikimedia Commons](https://upload.wikimedia.org/wikipedia/commons/8/80/World_map_-_low_resolution.svg) | Wikimedia Commons · CC BY-SA 3.0 |
| `trip_cover_default` | Cover | `public/marketing/covers/atlas-blue.svg` | _local vector_ | Custom gradient |
| `trip_cover_sunset` | Cover | `public/marketing/covers/atlas-sunset.svg` | _local vector_ | Custom gradient |
| `flag_pt` | Flag | `public/marketing/flags/pt.svg` | [FlagCDN](https://flagcdn.com/w40/pt.png) | FlagCDN · CC BY-SA 4.0 |
| `flag_jp` | Flag | `public/marketing/flags/jp.svg` | [FlagCDN](https://flagcdn.com/w40/jp.png) | FlagCDN · CC BY-SA 4.0 |
| `flag_eg` | Flag | `public/marketing/flags/eg.svg` | [FlagCDN](https://flagcdn.com/w40/eg.png) | FlagCDN · CC BY-SA 4.0 |
| `flag_us` | Flag | `public/marketing/flags/us.svg` | [FlagCDN](https://flagcdn.com/w40/us.png) | FlagCDN · CC BY-SA 4.0 |
| `flag_mx` | Flag | `public/marketing/flags/mx.svg` | [FlagCDN](https://flagcdn.com/w40/mx.png) | FlagCDN · CC BY-SA 4.0 |
| `gallery_camera` | Gallery | `public/marketing/gallery/camera.svg` | [Unsplash](https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05) | Unsplash · Vinta Supply Co. |
| `gallery_journal` | Gallery | `public/marketing/gallery/journal.svg` | [Unsplash](https://images.unsplash.com/photo-1483683804023-6ccdb62f86ef) | Unsplash · Rawpixel |
| `gallery_mountain` | Gallery | `public/marketing/gallery/mountain.svg` | [Unsplash](https://images.unsplash.com/photo-1500534623283-312aade485b7) | Unsplash · Nathan Anderson |
| `avatar_one` | Avatar | `public/marketing/avatars/one.svg` | [Unsplash](https://images.unsplash.com/photo-1504593811423-6dd665756598) | Unsplash · Brooke Cagle |
| `avatar_two` | Avatar | `public/marketing/avatars/two.svg` | [Unsplash](https://images.unsplash.com/photo-1521572163474-6864f9cf17ab) | Unsplash · Aiony Haust |
| `avatar_three` | Avatar | `public/marketing/avatars/three.svg` | [Unsplash](https://images.unsplash.com/photo-1438761681033-6461ffad8d80) | Unsplash · Marvin Meyer |

## Usage

- The `MarketingAssetRepository` will automatically use a CDN URL when it is present, otherwise it falls back to the local asset path (or a `MARKETING_ASSET_CDN` base if configured).
- To add more assets, extend `MarketingAssetSeeder` with a `path` (local fallback), optional `cdn_url`, and descriptive `meta` array that includes at least an `alt` string and a `source` reference for attribution.
- After editing the seeder, run `php artisan db:seed --class=MarketingAssetSeeder` (or `migrate:fresh --seed`) so the database stays in sync.
