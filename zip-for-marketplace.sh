rm -rf tmp
mkdir -p tmp/einvoice
cp -R classes tmp/einvoice
cp -R config tmp/einvoice
cp -R docs tmp/einvoice
cp -R override tmp/einvoice
cp -R sql tmp/einvoice
cp -R src tmp/einvoice
cp -R translations tmp/einvoice
cp -R views tmp/einvoice
cp -R upgrade tmp/einvoice
cp -R vendor tmp/einvoice
cp -R index.php tmp/einvoice
cp -R logo.png tmp/einvoice
cp -R einvoice.php tmp/einvoice
cp -R config.xml tmp/einvoice
cd tmp && find . -name ".DS_Store" -delete
zip -r einvoice.zip . -x ".*" -x "__MACOSX"
