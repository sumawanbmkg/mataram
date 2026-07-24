import os
import re

base_dir = r"D:\autoweb\var\www\webmataram"

def fix_links():
    html_files = []
    
    for root, dirs, files in os.walk(base_dir):
        if 'node_modules' in root or '.git' in root or 'backups' in root or 'admin' in root or 'khk' in root:
            continue
            
        for file in files:
            if file.endswith('.html'):
                html_files.append(os.path.join(root, file))

    for filepath in html_files:
        with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
            
        original_content = content
        
        # 1. kontak.html -> #
        content = content.replace('href="kontak.html"', 'href="#"')
        
        # 2. waktu.html -> tanda-waktu.html
        content = content.replace('href="waktu.html"', 'href="tanda-waktu.html"')
        
        # 3. petir.js -> remove
        content = re.sub(r'<script\s+src=["\']petir\.js["\'][^>]*></script>', '', content)
        
        # 4. magnet-bumi.js -> remove
        content = re.sub(r'<script\s+src=["\']magnet-bumi\.js["\'][^>]*></script>', '', content)
        
        # 5. /berita -> berita.html (only exact match)
        content = content.replace('href="/berita"', 'href="berita.html"')
        
        # 6. missing icons -> favicon.ico
        content = content.replace('href="/icons/favicon-32x32.png"', 'href="favicon.ico"')
        content = content.replace('href="/icons/apple-touch-icon.png"', 'href="favicon.ico"')
        content = content.replace('href="/icons/favicon-16x16.png"', 'href="favicon.ico"')
        
        if content != original_content:
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(content)
            print(f"Fixed links in {os.path.basename(filepath)}")

fix_links()
