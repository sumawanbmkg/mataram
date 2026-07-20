// scripts/fix_audit.ts
import * as fs from 'fs';
import * as path from 'path';

const rootDir = path.resolve('D:/autoweb/var/www/webmataram');
const reportPath = path.join(rootDir, 'audit_report.json');

interface Report {
  brokenRoutes: string[];
  missingAssets: string[];
  failedImports: string[];
}

const report: Report = JSON.parse(fs.readFileSync(reportPath, 'utf8'));

// 1x1 transparent PNG base64 placeholder
const placeholderPngBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/5+BAQAE/AL+U1F9MwAAAABJRU5ErkJggg==';
const placeholderBuffer = Buffer.from(placeholderPngBase64, 'base64');

function ensureDir(dir: string) {
  if (!fs.existsSync(dir)) {
    fs.mkdirSync(dir, { recursive: true });
  }
}

function createPlaceholder(filePath: string) {
  const dir = path.dirname(filePath);
  ensureDir(dir);
  if (filePath.match(/\.(png|jpg|jpeg|svg|gif)$/i)) {
    fs.writeFileSync(filePath, placeholderBuffer);
  } else if (filePath.match(/\.(html|php)$/i)) {
    const content = `<!DOCTYPE html>\n<html lang="en">\n<head><meta charset="UTF-8"><title>Placeholder</title></head>\n<body><h1>Page under construction</h1></body>\n</html>`;
    fs.writeFileSync(filePath, content, 'utf8');
  } else {
    fs.writeFileSync(filePath, '', 'utf8');
  }
  console.log('Placeholder created:', filePath);
}

// Fix missing assets
for (const asset of report.missingAssets) {
  const fullPath = path.resolve(asset);
  if (!fs.existsSync(fullPath)) {
    createPlaceholder(fullPath);
  }
}

// Fix broken routes (pages)
for (const route of report.brokenRoutes) {
  const fullPath = path.resolve(route);
  if (!fs.existsSync(fullPath)) {
    createPlaceholder(fullPath);
  }
}

console.log('Fix script completed.');
