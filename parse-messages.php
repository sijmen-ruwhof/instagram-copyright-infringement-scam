<?php
$handle = openDir('json/');
$rows = array();
while (false !== ($fileName = readDir($handle))) 
{
    if ($fileName == "." || $fileName == "..") 
        continue;

    list ($headers, $body) = explode("\r\n\r\n", file_get_contents('json/'.$fileName));

    $json = json_decode($body);
    forEach($json->inbox->threads as $thread)
    {        
        if (!isSet($thread->last_permanent_item->link))
            continue;

        $timestamp = subStr($thread->last_permanent_item->timestamp/100,0, -4);

        if (isSet($rows[$thread->last_permanent_item->timestamp]))
            exit('FATAL');

        $rows[$timestamp] = array(
            'username'      => $thread->users[0]->username,
            'full_name'     => $thread->users[0]->full_name,
            'is_verified'   => $thread->users[0]->is_verified,
            'is_private'    => $thread->users[0]->is_private,
            'is_friend'     => $thread->users[0]->friendship_status->following || $thread->users[0]->friendship_status->is_bestie,
            'timestamp'     => $timestamp,
            'text'          => $thread->last_permanent_item->link->text,
        );
    }
}
closeDir($handle);
krSort ($rows);

$lastTimestamp = time();
$privateCount = $followersCount = 0;
$followers = array(
    'nelsymichael' => '62100',
    '00_fulla_00' => '149000',
    'ketflixandpillsinc' => '513000',
    'cristinavelaoficial' => '192000',
    'carolinaaland' => '1000000',
    'drorkontento' => '144000',
    'mishkoyulia_92' => '146000',
    'glebve' => '969000',
    'na.mill' => '1800000',
    'aliabidov_' => '1100000',
    'vaagmk' => '642000',
    'justking31' => '2800000',
    'zusjeofficial' => '1300000',
    'moscow_dagi' => '76900',
    'golos.dagestana' => '1000000',
    'azamat_abu_muhammad1' => '67000',
    'i.s.nesquik' => '771000',
    'dimasblog' => '743000',
    'nastya_dreamy' => '104000',
    '_dilim_d_' => '126000',
    '_yyulduz' => '152000',
    'anjelika_kairatovna_official' => '2600000',
    'januaryharshe' => '53500',
    'veronikakoshkinaa' => '196000',
    'markiiskaa' => '174000',
    'aleksanderpakin' => '174000',
    'do_the_sport' => '2600000',
    'noa_mendel_' => '58100',
    'shura_azmv' => '65400',
    'gulayzeynalli' => '741000',
    'ya.molli' => '2200000',
    'german_gulyaev' => '76800',
    'transformation.way' => '41300',
);
print '
    <style>
	body, td 			{ font-family		: arial; 	}
	table 				{ border-collapse	: collapse; }
	tr:nth-child(odd)  	{ background-color	: #D9E1F2;	}
	tr:nth-child(even) 	{ background-color	: #FFFFFF;	}
	th { 
		text-align		: left;
		font-weight		: bold;
		color			: #FFFFFF;
		background-color: #4472C4;
		padding			: 4px;
	}
	td, th { 
		vertical-align	: top; 
		border			: 1px solid #000000;
		padding			: 4px;
	}
    </style>
    Alle accounts die op priv&eacute; staan waarnaar een bericht is gestuurd:<br><br>
    <table>
        <tr>
            <th>Gebruikersnaam</th>
            <th>Naam</th>
            <th>Verified?</th>
            <th>Aantal volgers</th>
            <th>Datum bericht</th>
            <th>Aantal minuten sinds vorig bericht</th>
            <th>Bericht</th>
        </tr>
';
forEach ($rows as $row)
{
    if (!$row['is_private'] || in_array($row['username'], array('qqqv', 'rumeysaeren0', 'z2nep_34', 'Instagrammer')))
        continue;

    $privateCount++;
    $followersCount += $followers[$row['username']];
    print '
        <tr>
            <td><nobr><a href="https://www.instagram.com/'.$row['username'].'" target="_blank">@'.$row['username'].'</a></nobr></td>
            <td><nobr>'.$row['full_name'].'</nobr></td>
            <td><nobr>'.($row['is_verified'] ? 'Verified' : '').'</nobr></td>
            <td><nobr>'.$followers[$row['username']].'</nobr></td>
            <td><nobr>'.date('Y-m-d H:i:s', $row['timestamp']).'</nobr></td>
            <td>'.round(($lastTimestamp - $row['timestamp']) / 60,1).'</td>
            <td><nobr>'.$row['text'].'</nobr></td>
        </tr>
    ';
    $lastTimestamp = $row['timestamp'];
}
print '
    </table>
    <br><br>
    Totaal aantal gehackte accounts: '.$privateCount.'<br>
    Totaal aantal followers van gehackte accounts: '.$followersCount.'<br>
    Gemiddeld aantal followers per gehackte account: '.round($followersCount / $privateCount).'<br>
    Totaal aantal uitgestuurde berichten: '.count($rows).'
    <br><br>
    <table>
';
$lastTimestamp = time();
forEach ($rows as $row)
{
    print '
        <tr>
            <td><nobr><a href="https://www.instagram.com/'.$row['username'].'" target="_blank">@'.$row['username'].'</a></nobr></td>
            <td><nobr>'.$row['full_name'].'</nobr></td>
            <td><nobr>'.($row['is_verified'] ? 'Verified' : '').'</nobr></td>
            <td><nobr>'.($row['is_private']  ? 'Private'  : '').'</nobr></td>
            <td><nobr>'.date('Y-m-d H:i:s', $row['timestamp']).'</nobr></td>
            <td>'.($lastTimestamp - $row['timestamp']).'</td>
            <td><nobr>'.$row['text'].'</nobr></td>
        </tr>
    ';
    $lastTimestamp = $row['timestamp'];
}
print '</table>';
