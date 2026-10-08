// Copie le JavaScript de Bootstrap et les polices Bootstrap Icons dans public/.
import { cpSync, mkdirSync } from 'node:fs';

mkdirSync('public/js', { recursive: true });
mkdirSync('public/fonts', { recursive: true });

cpSync('node_modules/bootstrap/dist/js/bootstrap.bundle.min.js', 'public/js/bootstrap.bundle.min.js');
cpSync('node_modules/bootstrap-icons/font/fonts', 'public/fonts', { recursive: true });

console.log('Assets copiés dans public/js et public/fonts');
