# Release build script for Flextype
# Copyright (c) Sergey Romanenko
# usage: bash release.sh [version] 

name="flextype";
version="$1";
curl "https://github.com/flextype/$name/archive/refs/tags/v$version.zip" -L -O;
unzip "v$version.zip";
rm "v$version.zip";
cd "$name-$version";
composer update;
rm -rf __MACOSX;
find . -name '.DS_Store' -type f -delete;
zip -r "$name-$version.zip" . ;
cd ../;
mv "$name-$version/$name-$version.zip" .;
rm -r "$name-$version/";