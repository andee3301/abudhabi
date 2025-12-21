<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::first();

if (!$user) {
    $user = \App\Models\User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password')
    ]);
    echo "✅ Created test user\n";
}

$token = $user->createToken('api-docs-testing', [
    'trips:read',
    'trips:write',
    'journal:read',
    'journal:write',
    'itinerary:read',
    'itinerary:write',
    'stats:read',
    'cities:read'
]);

echo "\n";
echo "===================================\n";
echo "🔑 API TOKEN (copy this):\n";
echo "===================================\n";
echo $token->plainTextToken . "\n";
echo "===================================\n";
echo "\n";
echo "📧 Email: " . $user->email . "\n";
echo "👤 User ID: " . $user->id . "\n";
echo "🎫 Abilities: All granted\n";
echo "\n";
echo "📖 How to use in /docs/api:\n";
echo "1. Visit http://localhost:8000/docs/api\n";
echo "2. Click 'Authorize' button (🔒 icon) at the top\n";
echo "3. Enter: Bearer {TOKEN}\n";
echo "   Example: Bearer " . substr($token->plainTextToken, 0, 20) . "...\n";
echo "4. Click 'Authorize' then 'Close'\n";
echo "5. Try any endpoint with 'Try it out'\n";
echo "\n";
