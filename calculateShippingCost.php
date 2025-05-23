<?php
/**
 * Calculate shipping cost based on weight tiers and destination zones for US only
 * 
 * @param string $zipCode Destination ZIP code
 * @param float $weight Package weight in pounds
 * @return float Shipping cost
 */
function calculateDomesticShipping($zipCode, $weight) {
    // Ensure zipCode is valid
    if (empty($zipCode) || !is_numeric($zipCode) || strlen($zipCode) < 5) {
        return 15.00; // Default rate if invalid ZIP
    }
    
    // Round up weight to nearest pound and ensure it's a positive number
    $weight = max(1, ceil(floatval($weight)));
    
    // Get the first 3 digits of zip code to determine zone
    $zipPrefix = substr($zipCode, 0, 3);
    
    // Define shipping zones - map ZIP code prefixes to zones (1-8)
    // Zone 1: Local/Nearby (OR, WA)
    // Zone 2: Western states
    // Zone 3: Central West
    // Zone 4: Central
    // Zone 5: Central East
    // Zone 6: Southeast
    // Zone 7: Mid-Atlantic
    // Zone 8: Northeast
    
    $zipToZone = [
        // Zone 1: Oregon, Washington (Local/Nearby)
        '970' => 1, '971' => 1, '972' => 1, '973' => 1, '974' => 1, '975' => 1, '976' => 1, '977' => 1,
        '978' => 1, '979' => 1, '980' => 1, '981' => 1, '982' => 1, '983' => 1, '984' => 1, '985' => 1,
        '986' => 1, '988' => 1, '989' => 1, '990' => 1, '991' => 1, '992' => 1, '993' => 1, '994' => 1,
        
        // Zone 2: Western states (CA, NV, ID, MT)
        '832' => 2, '834' => 2, '835' => 2, '836' => 2, '837' => 2, '838' => 2, '839' => 2, 
        '890' => 2, '891' => 2, '893' => 2, '895' => 2, '897' => 2, '898' => 2,
        '900' => 2, '901' => 2, '902' => 2, '903' => 2, '904' => 2, '905' => 2, '906' => 2,
        '907' => 2, '908' => 2, '910' => 2, '911' => 2, '912' => 2, '913' => 2, '914' => 2,
        '915' => 2, '916' => 2, '917' => 2, '918' => 2, '919' => 2, '920' => 2, '921' => 2,
        '922' => 2, '923' => 2, '924' => 2, '925' => 2, '926' => 2, '927' => 2, '928' => 2,
        '930' => 2, '931' => 2, '932' => 2, '933' => 2, '934' => 2, '935' => 2, '936' => 2,
        '937' => 2, '938' => 2, '939' => 2, '940' => 2, '941' => 2, '942' => 2, '943' => 2,
        '944' => 2, '945' => 2, '946' => 2, '947' => 2, '948' => 2, '949' => 2, '950' => 2,
        '951' => 2, '952' => 2, '953' => 2, '954' => 2, '955' => 2, '956' => 2, '957' => 2,
        '958' => 2, '959' => 2, '960' => 2, '961' => 2,
        '590' => 2, '591' => 2, '592' => 2, '593' => 2, '594' => 2, '595' => 2, '596' => 2,
        '597' => 2, '598' => 2, '599' => 2,
        
        // Zone 3: Central West (AZ, UT, WY, CO, NM)
        '800' => 3, '801' => 3, '802' => 3, '803' => 3, '804' => 3, '805' => 3, '806' => 3, 
        '807' => 3, '808' => 3, '809' => 3, '810' => 3, '811' => 3, '812' => 3, '813' => 3,
        '814' => 3, '815' => 3, '816' => 3,
        '820' => 3, '821' => 3, '822' => 3, '823' => 3, '824' => 3, '825' => 3, '826' => 3,
        '827' => 3, '828' => 3, '829' => 3, '830' => 3, '831' => 3,
        '840' => 3, '841' => 3, '842' => 3, '843' => 3, '844' => 3, '845' => 3, '846' => 3,
        '847' => 3,
        '850' => 3, '851' => 3, '852' => 3, '853' => 3, '855' => 3, '856' => 3, '857' => 3,
        '859' => 3, '860' => 3, '863' => 3, '864' => 3, '865' => 3,
        '870' => 3, '871' => 3, '872' => 3, '873' => 3, '874' => 3, '875' => 3, '877' => 3,
        '878' => 3, '879' => 3, '880' => 3, '881' => 3, '882' => 3, '883' => 3, '884' => 3,
        '885' => 3,
        
        // Zone 4: Central (ND, SD, NE, KS, OK, TX)
        '500' => 4, '501' => 4, '502' => 4, '503' => 4, '504' => 4, '505' => 4, '506' => 4,
        '507' => 4, '508' => 4, '509' => 4,
        '570' => 4, '571' => 4, '572' => 4, '573' => 4, '574' => 4, '575' => 4,
        '576' => 4, '577' => 4,
        '580' => 4, '581' => 4, '582' => 4, '583' => 4, '584' => 4, '585' => 4, '586' => 4,
        '587' => 4, '588' => 4,
        '730' => 4, '731' => 4, '734' => 4, '735' => 4, '736' => 4, '737' => 4, '738' => 4,
        '739' => 4,
        '740' => 4, '741' => 4, '743' => 4, '744' => 4, '745' => 4, '746' => 4, '747' => 4,
        '748' => 4, '749' => 4,
        '750' => 4, '751' => 4, '752' => 4, '753' => 4, '754' => 4, '755' => 4, '756' => 4,
        '757' => 4, '758' => 4, '759' => 4,
        '760' => 4, '761' => 4, '762' => 4, '763' => 4, '764' => 4, '765' => 4, '766' => 4,
        '767' => 4, '768' => 4, '769' => 4,
        '770' => 4, '771' => 4, '772' => 4, '773' => 4, '774' => 4, '775' => 4, '776' => 4,
        '777' => 4, '778' => 4, '779' => 4,
        '780' => 4, '781' => 4, '782' => 4, '783' => 4, '784' => 4, '785' => 4, '786' => 4,
        '787' => 4, '788' => 4, '789' => 4,
        '790' => 4, '791' => 4, '792' => 4, '793' => 4, '794' => 4, '795' => 4, '796' => 4,
        '797' => 4, '798' => 4, '799' => 4,
        
        // Zone 5: Central Midwest (MN, IA, MO, AR, WI, IL)
        '510' => 5, '511' => 5, '512' => 5, '513' => 5, '514' => 5, '515' => 5, '516' => 5,
        '520' => 5, '521' => 5, '522' => 5, '523' => 5, '524' => 5, '525' => 5, '526' => 5,
        '527' => 5, '528' => 5, '530' => 5, '531' => 5, '532' => 5, '534' => 5, '535' => 5,
        '537' => 5, '538' => 5, '539' => 5, '540' => 5, '541' => 5, '542' => 5, '543' => 5,
        '544' => 5, '545' => 5, '546' => 5, '547' => 5, '548' => 5, '549' => 5, '550' => 5,
        '551' => 5, '553' => 5, '554' => 5, '555' => 5, '556' => 5, '557' => 5, '558' => 5,
        '559' => 5, '560' => 5, '561' => 5, '562' => 5, '563' => 5, '564' => 5, '565' => 5,
        '566' => 5, '567' => 5,
        '600' => 5, '601' => 5, '602' => 5, '603' => 5, '604' => 5, '605' => 5, '606' => 5,
        '607' => 5, '608' => 5, '609' => 5, '610' => 5, '611' => 5, '612' => 5, '613' => 5,
        '614' => 5, '615' => 5, '616' => 5, '617' => 5, '618' => 5, '619' => 5, '620' => 5,
        '622' => 5, '623' => 5, '624' => 5, '625' => 5, '626' => 5, '627' => 5, '628' => 5,
        '629' => 5,
        '630' => 5, '631' => 5, '633' => 5, '634' => 5, '635' => 5, '636' => 5, '637' => 5,
        '638' => 5, '639' => 5, '640' => 5, '641' => 5, '644' => 5, '645' => 5, '646' => 5,
        '647' => 5, '648' => 5, '649' => 5, '650' => 5, '651' => 5, '652' => 5, '653' => 5,
        '654' => 5, '655' => 5, '656' => 5, '657' => 5, '658' => 5, '660' => 5, '661' => 5,
        '662' => 5, '664' => 5, '665' => 5, '666' => 5, '667' => 5, '668' => 5,
        '716' => 5, '717' => 5, '718' => 5, '719' => 5, '720' => 5, '721' => 5, '722' => 5,
        '723' => 5, '724' => 5, '725' => 5, '726' => 5, '727' => 5, '728' => 5, '729' => 5,
        
        // Zone 6: Southeast (LA, MS, AL, GA, FL, SC)
        '290' => 6, '291' => 6, '292' => 6, '293' => 6, '294' => 6, '295' => 6, '296' => 6,
        '297' => 6, '298' => 6, '299' => 6,
        '300' => 6, '301' => 6, '302' => 6, '303' => 6, '304' => 6, '305' => 6, '306' => 6,
        '307' => 6, '308' => 6, '309' => 6, '310' => 6, '311' => 6, '312' => 6, '313' => 6,
        '314' => 6, '315' => 6, '316' => 6, '317' => 6, '318' => 6, '319' => 6, '320' => 6,
        '321' => 6, '322' => 6, '323' => 6, '324' => 6, '325' => 6, '326' => 6, '327' => 6,
        '328' => 6, '329' => 6, '330' => 6, '331' => 6, '332' => 6, '333' => 6, '334' => 6,
        '335' => 6, '336' => 6, '337' => 6, '338' => 6, '339' => 6, '340' => 6, '341' => 6,
        '342' => 6, '344' => 6, '346' => 6, '347' => 6, '349' => 6,
        '350' => 6, '351' => 6, '352' => 6, '354' => 6, '355' => 6, '356' => 6, '357' => 6,
        '358' => 6, '359' => 6, '360' => 6, '361' => 6, '362' => 6, '363' => 6, '364' => 6,
        '365' => 6, '366' => 6, '367' => 6, '368' => 6, '369' => 6, '370' => 6, '371' => 6,
        '372' => 6, '373' => 6, '374' => 6, '375' => 6, '376' => 6, '377' => 6, '378' => 6,
        '379' => 6, '380' => 6, '381' => 6, '382' => 6, '383' => 6, '384' => 6, '385' => 6,
        '386' => 6, '387' => 6, '388' => 6, '389' => 6, '390' => 6, '391' => 6, '392' => 6,
        '393' => 6, '394' => 6, '395' => 6, '396' => 6, '397' => 6, '398' => 6, '399' => 6,
        '700' => 6, '701' => 6, '703' => 6, '704' => 6, '705' => 6, '706' => 6, '707' => 6,
        '708' => 6, '710' => 6, '711' => 6, '712' => 6, '713' => 6, '714' => 6,
        
        // Zone 7: Mid Atlantic (KY, TN, WV, VA, NC)
        '230' => 7, '231' => 7, '232' => 7, '233' => 7, '234' => 7, '235' => 7, '236' => 7,
        '237' => 7, '238' => 7, '239' => 7, '240' => 7, '241' => 7, '242' => 7, '243' => 7,
        '244' => 7, '245' => 7, '246' => 7, '247' => 7, '248' => 7, '249' => 7,
        '250' => 7, '251' => 7, '252' => 7, '253' => 7, '254' => 7, '255' => 7, '256' => 7,
        '257' => 7, '258' => 7, '259' => 7, '260' => 7, '261' => 7, '262' => 7, '263' => 7,
        '264' => 7, '265' => 7, '266' => 7, '267' => 7, '268' => 7, '270' => 7, '271' => 7,
        '272' => 7, '273' => 7, '274' => 7, '275' => 7, '276' => 7, '277' => 7, '278' => 7,
        '279' => 7, '280' => 7, '281' => 7, '282' => 7, '283' => 7, '284' => 7, '285' => 7,
        '286' => 7, '287' => 7, '288' => 7, '289' => 7,
        '400' => 7, '401' => 7, '402' => 7, '403' => 7, '404' => 7, '405' => 7, '406' => 7,
        '407' => 7, '408' => 7, '409' => 7, '410' => 7, '411' => 7, '412' => 7, '413' => 7,
        '414' => 7, '415' => 7, '416' => 7, '417' => 7, '418' => 7, '420' => 7, '421' => 7,
        '422' => 7, '423' => 7, '424' => 7, '425' => 7, '426' => 7, '427' => 7,
        '430' => 7, '431' => 7, '432' => 7, '433' => 7, '434' => 7, '435' => 7, '436' => 7,
        '437' => 7, '438' => 7, '439' => 7, '440' => 7, '441' => 7, '442' => 7, '443' => 7,
        '444' => 7, '445' => 7, '446' => 7, '447' => 7, '448' => 7, '449' => 7,
        '450' => 7, '451' => 7, '452' => 7, '453' => 7, '454' => 7, '455' => 7, '456' => 7,
        '457' => 7, '458' => 7, '459' => 7, '460' => 7, '461' => 7, '462' => 7, '463' => 7,
        '464' => 7, '465' => 7, '466' => 7, '467' => 7, '468' => 7, '469' => 7,
        '470' => 7, '471' => 7, '472' => 7, '473' => 7, '474' => 7, '475' => 7, '476' => 7,
        '477' => 7, '478' => 7, '479' => 7,
        
        // Zone 8: Northeast (PA, NJ, NY, CT, RI, MA, VT, NH, ME, DE, MD, DC)
        '010' => 8, '011' => 8, '012' => 8, '013' => 8, '014' => 8, '015' => 8, '016' => 8,
        '017' => 8, '018' => 8, '019' => 8, '020' => 8, '021' => 8, '022' => 8, '023' => 8,
        '024' => 8, '025' => 8, '026' => 8, '027' => 8, '028' => 8, '029' => 8, '030' => 8,
        '031' => 8, '032' => 8, '033' => 8, '034' => 8, '035' => 8, '036' => 8, '037' => 8,
        '038' => 8, '039' => 8, '040' => 8, '041' => 8, '042' => 8, '043' => 8, '044' => 8,
        '045' => 8, '046' => 8, '047' => 8, '048' => 8, '049' => 8, '050' => 8, '051' => 8,
        '052' => 8, '053' => 8, '054' => 8, '055' => 8, '056' => 8, '057' => 8, '058' => 8,
        '059' => 8, '060' => 8, '061' => 8, '062' => 8, '063' => 8, '064' => 8, '065' => 8,
        '066' => 8, '067' => 8, '068' => 8, '069' => 8, '070' => 8, '071' => 8, '072' => 8,
        '073' => 8, '074' => 8, '075' => 8, '076' => 8, '077' => 8, '078' => 8, '079' => 8,
        '080' => 8, '081' => 8, '082' => 8, '083' => 8, '084' => 8, '085' => 8, '086' => 8,
        '087' => 8, '088' => 8, '089' => 8, '090' => 8, '091' => 8, '092' => 8, '093' => 8,
        '094' => 8, '095' => 8, '096' => 8, '097' => 8, '098' => 8, '099' => 8, '100' => 8,
        '101' => 8, '102' => 8, '103' => 8, '104' => 8, '105' => 8, '106' => 8, '107' => 8,
        '108' => 8, '109' => 8, '110' => 8, '111' => 8, '112' => 8, '113' => 8, '114' => 8,
        '115' => 8, '116' => 8, '117' => 8, '118' => 8, '119' => 8, '120' => 8, '121' => 8,
        '122' => 8, '123' => 8, '124' => 8, '125' => 8, '126' => 8, '127' => 8, '128' => 8,
        '129' => 8, '130' => 8, '131' => 8, '132' => 8, '133' => 8, '134' => 8, '135' => 8,
        '136' => 8, '137' => 8, '138' => 8, '139' => 8, '140' => 8, '141' => 8, '142' => 8,
        '143' => 8, '144' => 8, '145' => 8, '146' => 8, '147' => 8, '148' => 8, '149' => 8,
        '150' => 8, '151' => 8, '152' => 8, '153' => 8, '154' => 8, '155' => 8, '156' => 8,
        '157' => 8, '158' => 8, '159' => 8, '160' => 8, '161' => 8, '162' => 8, '163' => 8,
        '164' => 8, '165' => 8, '166' => 8, '167' => 8, '168' => 8, '169' => 8, '170' => 8,
        '171' => 8, '172' => 8, '173' => 8, '174' => 8, '175' => 8, '176' => 8, '177' => 8,
        '178' => 8, '179' => 8, '180' => 8, '181' => 8, '182' => 8, '183' => 8, '184' => 8,
        '185' => 8, '186' => 8, '187' => 8, '188' => 8, '189' => 8, '190' => 8, '191' => 8,
        '192' => 8, '193' => 8, '194' => 8, '195' => 8, '196' => 8, '197' => 8, '198' => 8,
        '199' => 8, '200' => 8, '201' => 8, '202' => 8, '203' => 8, '204' => 8, '205' => 8,
        '206' => 8, '207' => 8, '208' => 8, '209' => 8, '210' => 8, '211' => 8, '212' => 8,
        '214' => 8, '215' => 8, '216' => 8, '217' => 8, '218' => 8, '219' => 8, '220' => 8,
        '221' => 8, '222' => 8, '223' => 8, '224' => 8, '225' => 8, '226' => 8, '227' => 8,
        '228' => 8, '229' => 8
    ];
    
    // Determine the zone based on ZIP prefix or default to zone 4 (central)
    $zone = isset($zipToZone[$zipPrefix]) ? $zipToZone[$zipPrefix] : 4;
    
    // Define weight tiers and base rates (per zone)
    // Format: 'max_weight' => [zone1_rate, zone2_rate, ..., zone8_rate]
    $weightTiers = [
        1 => [8.95, 9.95, 10.95, 11.95, 12.95, 13.95, 14.95, 15.95],
        2 => [9.95, 10.95, 12.95, 13.95, 14.95, 15.95, 16.95, 17.95],
        3 => [10.95, 12.95, 14.95, 16.95, 17.95, 18.95, 19.95, 20.95],
        5 => [12.95, 14.95, 16.95, 18.95, 20.95, 22.95, 24.95, 26.95],
        10 => [16.95, 19.95, 22.95, 25.95, 27.95, 30.95, 32.95, 34.95],
        15 => [20.95, 24.95, 27.95, 30.95, 33.95, 36.95, 39.95, 42.95],
        20 => [24.95, 28.95, 32.95, 35.95, 39.95, 43.95, 47.95, 51.95],
        25 => [28.95, 33.95, 37.95, 41.95, 45.95, 50.95, 55.95, 60.95],
        30 => [33.95, 38.95, 43.95, 47.95, 52.95, 58.95, 64.95, 69.95],
        35 => [38.95, 44.95, 50.95, 54.95, 59.95, 65.95, 71.95, 77.95],
        40 => [43.95, 50.95, 57.95, 62.95, 67.95, 73.95, 79.95, 85.95],
        45 => [48.95, 56.95, 64.95, 69.95, 75.95, 81.95, 87.95, 93.95],
        50 => [53.95, 62.95, 71.95, 77.95, 83.95, 89.95, 96.95, 103.95],
        60 => [63.95, 73.95, 83.95, 89.95, 96.95, 103.95, 111.95, 119.95],
        70 => [73.95, 84.95, 95.95, 102.95, 109.95, 117.95, 125.95, 133.95],
        80 => [83.95, 95.95, 107.95, 115.95, 123.95, 131.95, 139.95, 147.95],
        90 => [93.95, 106.95, 119.95, 128.95, 137.95, 145.95, 153.95, 161.95],
        100 => [103.95, 117.95, 131.95, 141.95, 151.95, 159.95, 167.95, 175.95],
    ];
    
    // Cap weight at 100 lbs (maximum UPS standard)
    if ($weight > 100) {
        $weight = 100;
    }
    
    // Find the appropriate weight tier
    $applicableTier = 100; // Default to maximum tier
    foreach ($weightTiers as $tierWeight => $rates) {
        if ($weight <= $tierWeight) {
            $applicableTier = $tierWeight;
            break;
        }
    }
    
    // Calculate shipping - use zone index (0-based)
    $zoneIndex = $zone - 1;
    $shippingCost = $weightTiers[$applicableTier][$zoneIndex];
    
    // Add handling fee for larger shipments
    if ($weight > 30) {
        $shippingCost += 5.00; // Additional handling fee for larger packages
    }
    
    // Round to 2 decimal places
    return round($shippingCost, 2);
}