chmod 644 *.php
chmod 755 css
chmod 755 js
chmod 755 uploads
chmod 755 Scripts
cd css
chmod 644 *.css
cd ..
cd js
chmod 644 *.js
cd ..
cd Scripts
chmod 644 *.js
cd ..
cd uploads
for d in */; do
    chmod 755 "$d"
    cd "$d"
    for f in *; do
        chmod 644 "$f"
    done
    cd ..
done

