#!/bin/bash
cd ../
rm -rf app
mkdir app
cd app
wget -O app.zip https://github.com/ZheGal/offers-sites-connector/archive/main.zip
unzip -o app.zip
rm -rf app.zip
cd offers-sites-connector-main
zip -r app.zip .
mv app.zip ../
cd ../
rm -rf offers-sites-connector-main
unzip -o app.zip
rm -rf app.zip