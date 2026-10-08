// Copie le JavaScript de Bootstrap et les polices Bootstrap Icons dans public/.
import { copyFileSync, mkdirSync, readdirSync } from 'node:fs';

mkdirSync('public/js', { recursive: true });
mkdirSync('public/fonts', { recursive: true });

copyFileSync('node_modules/bootstrap/dist/js/bootstrap.bundle.min.js', 'public/js/bootstrap.bundle.min.js');
// cpSync récursif plante sous Windows avec Node 24 quand le chemin contient des accents.
const fontsDir = 'node_modules/bootstrap-icons/font/fonts';
for (const file of readdirSync(fontsDir)) {
  copyFileSync(`${fontsDir}/${file}`, `public/fonts/${file}`);
}

console.log('Assets copiés dans public/js et public/fonts');
