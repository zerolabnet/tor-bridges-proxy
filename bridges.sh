#!/bin/bash

# Change bridge
BRIDGES="/etc/tor/bridges.txt"
echo "HardwareAccel 1" > /etc/tor/torrc
echo "Log notice file /var/log/tor/notices.log" >> /etc/tor/torrc
echo "DNSPort 0.0.0.0:9053" >> /etc/tor/torrc
echo "SocksPort 0.0.0.0:9150" >> /etc/tor/torrc
echo "DataDirectory /var/lib/tor" >> /etc/tor/torrc
echo "ExcludeExitNodes {us},{ca},{cn},{hk},{jp},{kr},{tw},{ru},{ua},{by},{kz},{in},{af},{aq},{ar},{au},{bs},{bh},{bb},{bz},{bo},{bw},{br},{bn},{bf},{bi},{kh},{cm},{cv},{ky},{cf},{td},{cl},{co},{km},{cg},{cd},{ck},{cr},{ci},{cu},{dj},{dm},{do},{ec},{eg},{sv},{gq},{et},{fk},{fo},{fj},{ga},{gm},{gh},{gi},{gl},{gd},{gp},{gu},{gt},{gn},{gw},{gy},{ht},{hn},{id},{ir},{iq},{il},{jm},{jo},{ke},{ki},{kp},{kg},{lb},{ls},{lr},{ly},{mo},{mg},{mw},{my},{mv},{ml},{mt},{mh},{mq},{mr},{mu},{yt},{mx},{fm},{mn},{ms},{ma},{mz},{mm},{na},{nr},{np},{nc},{nz},{ni},{ne},{ng},{nu},{nf},{mp},{om},{pk},{pw},{ps},{pa},{pg},{py},{pe},{ph},{pr},{qa},{re},{rw},{ws},{st},{sa},{sn},{sc},{sl},{sb},{so},{as},{za},{lk},{kn},{lc},{pm},{vc},{sd},{sr},{sz},{sy},{tj},{tz},{th},{tg},{tk},{to},{tt},{tn},{tr},{tm},{tc},{tv},{ug},{ae},{uy},{vu},{vn},{vi},{wf},{ye},{zm},{zw},{??}" >> /etc/tor/torrc
echo "StrictNodes 1" >> /etc/tor/torrc
if [[ ! -z $(grep '[^[:space:]]' ${BRIDGES}) ]]; then
	echo "UseBridges 1" >> /etc/tor/torrc
	echo "ClientTransportPlugin obfs4 exec /usr/bin/obfs4proxy" >> /etc/tor/torrc
	while read BRIDGE; do
		echo "Bridge ${BRIDGE}" >> /etc/tor/torrc
	done < ${BRIDGES}
fi

# Clear log
printf ">_ created by ZeroChaos. Visit site: https://zerolab.net\n" > /var/log/tor/notices.log

# Reload torrc
pkill tor
sleep 1
/usr/bin/tor -f /etc/tor/torrc &