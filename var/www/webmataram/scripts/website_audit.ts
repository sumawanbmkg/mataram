// scripts/website_audit.ts
import * as fs from 'fs';
import * as path from 'path';
import fetch from 'node-fetch';

interface Report {
  brokenRoutes: string[];
  missingAssets: string[];
  failedImports: string[];
}

const rootDir = path.resolve('D:/autoweb/var/www/webmataram');

function getAllFiles(dir: string, exts: string[]): string[] {
  const entries = fs.readdirSync(dir, { withFileTypes: true });
  const files: string[] = [];
  for (const entry of entries) {
    const fullPath = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      files.push(...getAllFiles(fullPath, exts));
    } else if (exts.includes(path.extname(entry.name).toLowerCase())) {
      files.push(fullPath);
    }
  }
  return files;
}

function extractLinks(html: string): string[] {
  const regex = /<(?:a|link|script|img)\s+[^>]*(?:href|src)\s*=\s*"([^"]+)"/gi;
  const links: string[] = [];
  let m;
  while ((m = regex.exec(html)) !== null) {
    links.push(m[1]);
  }
  return links;
}

async function checkUrl(url: string): Promise<number> {
  try {
    const resp = await fetch(url, { method: 'HEAD' });
    return resp.status;
  } catch {
    return 0;
  }
}

async function runAudit(): Promise<Report> {
  const report: Report = { brokenRoutes: [], missingAssets: [], failedImports: [] };

  // Crawl HTML/PHP files
  const htmlFiles = getAllFiles(rootDir, ['.html', '.php']);
  for (const file of htmlFiles) {
    const content = fs.readFileSync(file, 'utf8');
    const links = extractLinks(content);
    for (const link of links) {
      if (link.startsWith('http')) {
        const status = await checkUrl(link);
        if (status >= 400) report.brokenRoutes.push(`${link} (status ${status})`);
      } else if (link.startsWith('#')) {
        continue;
      } else {
        const localPath = path.join(rootDir, link);
        if (!fs.existsSync(localPath)) {
          const ext = path.extname(localPath).toLowerCase();
          if (['.png', '.jpg', '.jpeg', '.svg', '.gif', '.css', '.js', '.ico', '.webp'].includes(ext)) {
            report.missingAssets.push(localPath);
          } else {
            report.brokenRoutes.push(localPath);
          }
        }
      }
    }
  }

  // Check imports in TS/JS files
  const srcFiles = getAllFiles(rootDir, ['.ts', '.tsx', '.js', '.jsx']);
  const importRegex = /import\s+.*?from\s+['"]([^'\"]+)['"]/g;
  for (const src of srcFiles) {
    const content = fs.readFileSync(src, 'utf8');
    let m;
    while ((m = importRegex.exec(content)) !== null) {
      const imp = m[1];
      if (imp.startsWith('.') || imp.startsWith('/')) {
        const resolved = path.resolve(path.dirname(src), imp);
        const candidates = [resolved, resolved + '.ts', resolved + '.js', path.join(resolved, 'index.ts'), path.join(resolved, 'index.js')];
        if (!candidates.some(p => fs.existsSync(p))) {
          report.failedImports.push(`${src} -> ${imp}`);
        }
      }
    }
  }

  return report;
}

runAudit().then(report => {
  const outPath = path.join(rootDir, 'audit_report.json');
  fs.writeFileSync(outPath, JSON.stringify(report, null, 2), 'utf8');
  console.log('Audit complete. Report saved to', outPath);
});
