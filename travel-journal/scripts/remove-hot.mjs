import { existsSync, rmSync } from 'node:fs';
import { resolve } from 'node:path';

const hotPath = resolve('public', 'hot');

if (existsSync(hotPath)) {
    rmSync(hotPath);
    console.log(`Removed stale Vite hot file at ${hotPath}`);
} else {
    console.log('No Vite hot file detected');
}
