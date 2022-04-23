#!/bin/bash

PWD_PATH="/var/www/pwd.php"

if [ ! -f ${PWD_PATH} ]; then
	password=$(tr -dc A-Za-z0-9 </dev/urandom | head -c 20 ; echo '')
	salt1=$(tr -dc A-Za-z0-9 </dev/urandom | head -c 20 ; echo '')
	salt2=$(tr -dc A-Za-z0-9 </dev/urandom | head -c 20 ; echo '')
	bcrypt_hash=$(htpasswd -bnBC 10 "" ${password} | tr -d ':\n' ; echo '')
	cat << EOF > ${PWD_PATH}
<?php
\$password = '${bcrypt_hash}';
\$salt1 = '${salt1}';
\$salt2 = '${salt2}';
?>
EOF
	echo "Your login password: ${password}"
fi