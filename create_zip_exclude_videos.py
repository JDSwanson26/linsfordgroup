import zipfile
import os
from pathlib import Path

root = Path(r"C:\Users\USER-PC\Desktop\New folder")
out = Path(r"C:\Users\USER-PC\Desktop\day 0.zip")
with zipfile.ZipFile(out, 'w', zipfile.ZIP_DEFLATED) as z:
    count = 0
    for p in root.rglob('*'):
        if p.is_file():
            # exclude anything under a 'videos' folder
            parts = [part.lower() for part in p.parts]
            if 'videos' in parts:
                continue
            z.write(p, arcname=os.path.relpath(p, root))
            count += 1
print(f'wrote {count} files to {out}')
