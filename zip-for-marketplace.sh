rm -rf tmp
mkdir -p tmp/electronicinvoicefields
cp -R classes tmp/electronicinvoicefields
cp -R config tmp/electronicinvoicefields
cp -R docs tmp/electronicinvoicefields
cp -R override tmp/electronicinvoicefields
cp -R sql tmp/electronicinvoicefields
cp -R src tmp/electronicinvoicefields
cp -R translations tmp/electronicinvoicefields
cp -R views tmp/electronicinvoicefields
cp -R upgrade tmp/electronicinvoicefields
cp -R vendor tmp/electronicinvoicefields
cp -R index.php tmp/electronicinvoicefields
cp -R logo.png tmp/electronicinvoicefields
cp -R electronicinvoicefields.php tmp/electronicinvoicefields
cp -R config.xml tmp/electronicinvoicefields
cp -R LICENSE tmp/electronicinvoicefields
cp -R README.md tmp/electronicinvoicefields
cd tmp && find . -name ".DS_Store" -delete
zip -r electronicinvoicefields.zip . -x ".*" -x "__MACOSX"
