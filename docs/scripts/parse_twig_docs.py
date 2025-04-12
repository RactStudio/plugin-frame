import os
import frontmatter
from pathlib import Path

output = []
for root, _, files in os.walk('templates'):
    for file in files:
        if file.endswith('.twig'):
            path = Path(root) / file
            with open(path) as f:
                content = frontmatter.load(f)
                
            output.append(f"## {file}\n")
            output.append(f"**Location**: `{path}`\n\n")
            if content.metadata:
                output.append("### Metadata\n")
                for k, v in content.metadata.items():
                    output.append(f"- **{k}**: {v}\n")
            output.append("\n---\n")

with open('public/twig.md', 'w') as f:
    f.write("# Twig Template Reference\n\n")
    f.write("".join(output))