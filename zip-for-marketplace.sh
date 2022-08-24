rm -rf tmp
mkdir -p tmp/electronic_invoice_fields
cp -R classes tmp/electronic_invoice_fields
cp -R config tmp/electronic_invoice_fields
cp -R docs tmp/electronic_invoice_fields
cp -R override tmp/electronic_invoice_fields
cp -R sql tmp/electronic_invoice_fields
cp -R src tmp/electronic_invoice_fields
cp -R translations tmp/electronic_invoice_fields
cp -R views tmp/electronic_invoice_fields
cp -R upgrade tmp/electronic_invoice_fields
cp -R vendor tmp/electronic_invoice_fields
cp -R index.php tmp/electronic_invoice_fields
cp -R logo.png tmp/electronic_invoice_fields
cp -R electronic_invoice_fields.php tmp/electronic_invoice_fields
cp -R config.xml tmp/electronic_invoice_fields
cp -R LICENSE tmp/electronic_invoice_fields
cp -R README.md tmp/electronic_invoice_fields
cd tmp && find . -name ".DS_Store" -delete
zip -r electronic_invoice_fields.zip . -x ".*" -x "__MACOSX"
