#!/bin/bash

VERSION=$(grep "^Version:" style.css | awk '{print $2}')
RELEASE="wheellab-$VERSION"
TMP="/tmp/$RELEASE"

rm -rf "$TMP"
mkdir -p "$TMP"

rsync -a ./ "$TMP/" \
  --exclude=".git" \
  --exclude=".github" \
  --exclude=".claude" \
  --exclude="node_modules" \
  --exclude="src" \
  --exclude="docs" \
  --exclude=".DS_Store" \
  --exclude=".gitignore" \
  --exclude=".editorconfig" \
  --exclude=".stylelintrc.json" \
  --exclude="eslint.config.js" \
  --exclude="package.json" \
  --exclude="package-lock.json" \
  --exclude="gulpfile.js" \
  --exclude="generate-sections.js" \
  --exclude="CLAUDE.md" \
  --exclude="README.md" \
  --exclude="release.sh" \
  --exclude="*.zip"

cd /tmp
zip -qr "$RELEASE.zip" "$RELEASE"

mv "$RELEASE.zip" "$OLDPWD/"
rm -rf "$TMP"

echo "Created: $RELEASE.zip"
