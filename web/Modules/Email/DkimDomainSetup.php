<?php

namespace Modules\Email;

class DkimDomainSetup
{

    public static function run($domain)
    {
        $dkimPrivateKeyFile = '/etc/opendkim/keys/'.$domain.'/dkim.private';
        $dkimTextFile = '/etc/opendkim/keys/'.$domain.'/dkim.txt';

        if (is_file($dkimPrivateKeyFile)) {
            return [
                'privateKey' => file_get_contents($dkimPrivateKeyFile),
                'text' => file_get_contents($dkimTextFile),
            ];
        }

        shell_exec('sudo mkdir -p /etc/opendkim/keys/'.$domain);
        shell_exec('sudo chown -R opendkim:opendkim /etc/opendkim/keys/'.$domain);
        shell_exec('sudo chmod go-rw /etc/opendkim/keys/'.$domain);

        $output = shell_exec('sudo opendkim-genkey -b 2048 -D /etc/opendkim/keys/'.$domain.' -h rsa-sha256 -r -s dkim -d '.$domain.' -v');

        $dkimPrivateKey = file_get_contents($dkimPrivateKeyFile);
        $dkimText = file_get_contents($dkimTextFile);

        return [
            'privateKey' => $dkimPrivateKey,
            'text' => $dkimText,
        ];
    }
}
