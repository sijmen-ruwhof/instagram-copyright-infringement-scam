<?php
 $searches = array(
     'inst',
     'lnst',
 );
$searches2 = array(
    'copy',
    'copr',
    'coop',
    'cpy',
    'righ',
    'rlgh',
);
$domains = array();

# merge the known phishing list with new found domains:
forEach (array_merge(explode("\n", file_get_contents('https://gitlab.com/intr0/iVOID.GitLab.io/raw/master/iVOID.hosts')), array(
    'copyrights-application.tk',
    'copyright-application.tk',
    'copyrights-apply.ml',
    'copyrights-appeals.tk',
    'copyright-appeals.tk',
    'supports-copyrights.tk',
    'instagramhelpnotice.com',
    'copyrightcenter-instagram.com',
)) as $line)
{
    if (subStr($line, 0, 1) == '#')
        continue;

    $line   = str_replace('0.0.0.0 ', '', $line);
    $pieces = explode('.', $line);
      forEach ($searches as $search)
         if (strPos($line, $search) !== false)
            forEach ($searches2 as $search2)
                if (strPos($line, $search2) !== false)
                    for ($i = 0; $i < count($pieces); $i++)
                        if (
                           strPos($pieces[$i], $search)  !== false ||
                            strPos($pieces[$i], $search2) !== false
                        ) {
                            $fqdn = '';
                            for ($j = $i; $j < count($pieces); $j++)
                                $fqdn .= (!empty($fqdn) ? '.' : '').$pieces[$j];
                
                            $domains[$fqdn] = 1;
                            break 2;
                        }
}
kSort($domains);

$tlds = array();
forEach ($domains as $domain => $null)
{
    $pieces = explode('.', $domain);
    $tld = $pieces[count($pieces) - 1];
    if (!isSet($tlds[$tld]))
        $tlds[$tld] = 0;

    $tlds[$tld]++;
}
arSort($tlds);


print '
    Total amount of domain names: '.count($domains).'<br><br>
    Total amount of domain names per TLD:<br><br>
';
print '<table>';
forEach ($tlds as $tld => $total)
    print "<tr><td>$tld</td><td>$total</td></tr>";
print '</table><br><br>All domain names:<br><br>';

forEach ($domains as $domain => $null)
{   
    print "$domain<br>";
    # print "<a href='http://$domain' target='_blank'>$domain</a><br>";
}
