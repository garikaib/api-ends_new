<?php

function zpOrderReceivedvars($qvars)
{

    $qvars[] = 'token';
    $qvars[] = 'PayerID';

    $qvars[] = 'innbucks';
    $qvars[] = 'order_num';
    $qvars[] = 'usd_amount';

    return $qvars;

}

add_filter('query_vars', 'zpOrderReceivedvars');
