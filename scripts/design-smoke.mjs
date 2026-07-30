#!/usr/bin/env node
/**
 * Lightweight design-system smoke check.
 *
 * Greps the built CSS/JS for selectors and tokens that must survive refactors.
 * Run after `npm run build`. Does not need a browser — catches missing
 * interaction overlays, Magic Search panel, and theme entries.
 *
 * Usage: node scripts/design-smoke.mjs
 * Exit 0 = pass, 1 = fail.
 */
import { readFileSync, existsSync } from 'node:fs';
import { resolve, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const cssPath = resolve(root, 'public/build/assets/app.css');
const jsPath = resolve(root, 'public/build/assets/app.js');
const hhentaiPath = resolve(root, 'public/build/assets/hhentai-theme.css');
const pornguruPath = resolve(root, 'public/build/assets/pornguru-theme.css');

const requiredFiles = [
  ['app.css', cssPath],
  ['app.js', jsPath],
  ['hhentai-theme.css', hhentaiPath],
  ['pornguru-theme.css', pornguruPath],
];

const cssMustContain = [
  '.search-promo',
  '.search-example',
  '.form-uploader',
  '.form-uploader-job',
  '.form-multipart',
  '.form-multipart-panel',
  '.sidebar-link:not(.sidebar-link--active):hover',
  '.panel-list-item:hover',
  '.search-item:hover',
  '--overlay-hover',
  'background-image:linear-gradient(var(--overlay-hover)',
];

const jsMustContain = [
  'data-search-panel',
  'data-search-promo-toggle',
  'data-uploader',
  'data-multipart',
  'openModals', // ModalManager class (name may minify; field is stable)
];

let failed = 0;

function fail(msg) {
  console.error(`FAIL  ${msg}`);
  failed += 1;
}

function ok(msg) {
  console.log(`ok    ${msg}`);
}

for (const [label, path] of requiredFiles) {
  if (!existsSync(path)) fail(`missing ${label} at ${path}`);
  else ok(`found ${label}`);
}

if (!existsSync(cssPath) || !existsSync(jsPath)) {
  console.error('\nRun `npm run build` first.');
  process.exit(1);
}

const css = readFileSync(cssPath, 'utf8');
const js = readFileSync(jsPath, 'utf8');

for (const needle of cssMustContain) {
  if (!css.includes(needle)) fail(`app.css missing: ${needle}`);
  else ok(`css has ${needle}`);
}

for (const needle of jsMustContain) {
  if (!js.includes(needle)) fail(`app.js missing: ${needle}`);
  else ok(`js has ${needle}`);
}

if (existsSync(pornguruPath)) {
  const theme = readFileSync(pornguruPath, 'utf8');
  if (!theme.includes('--main1') || !theme.includes('#ff6b4a')) {
    fail('pornguru-theme.css missing coral --main1');
  } else {
    ok('pornguru-theme.css has coral --main1');
  }
}

if (failed > 0) {
  console.error(`\n${failed} check(s) failed.`);
  process.exit(1);
}

console.log('\nAll design-smoke checks passed.');
