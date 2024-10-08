PHYRE_PHP=/usr/local/phyre/php/bin/php

systemctl stop phyre
apt-remove phyre -y

OS=$(lsb_release -si)
OS_LOWER=$(echo $OS | tr '[:upper:]' '[:lower:]')
OS_VERSION=$(lsb_release -sr)

rm -rf /usr/local/phyre/update/nginx
mkdir -p /usr/local/phyre/update/nginx
wget https://github.com/PhyreApps/PhyrePanelNGINX/raw/main/compilators/debian/nginx/dist/phyre-nginx-1.24.0-$OS_LOWER-$OS_VERSION.deb -O /usr/local/phyre/update/nginx/phyre-nginx-1.24.0-$OS_LOWER-$OS_VERSION.deb
dpkg -i /usr/local/phyre/update/nginx/phyre-nginx-1.24.0-$OS_LOWER-$OS_VERSION.deb

#
printf "Updating the panel...\n"
wget https://raw.githubusercontent.com/PhyreApps/PhyrePanelNGINX/main/compilators/debian/nginx/nginx.conf -O /usr/local/phyre/nginx/conf/nginx.conf
#
mkdir -p /usr/local/phyre/ssl
cp /usr/local/phyre/web/server/ssl/phyre.crt /usr/local/phyre/ssl/phyre.crt
cp /usr/local/phyre/web/server/ssl/phyre.key /usr/local/phyre/ssl/phyre.key

systemctl restart phyre
#systemctl status phyre

printf "Updating the database...\n"
$PHYRE_PHP /usr/local/phyre/web/artisan migrate
#$PHYRE_PHP artisan l5-swagger:generate
