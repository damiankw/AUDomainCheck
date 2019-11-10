<?php
  // set up the dictionary
  $DIC = pspell_new("en");

  // collect domain lists (expired, expiring)
  $EXP_CSV = file_get_contents('https://afilias.com.au/domain-drop-lists/expired');
  $DEL_CSV = file_get_contents('https://afilias.com.au/domain-drop-lists/deleted');

  // convert to array
  $EXP = str_getcsv($EXP_CSV);
  $DEL = str_getcsv($DEL_CSV);

  // put headers in the correct place
  $EXP = array_map('str_getcsv', explode(PHP_EOL, $EXP_CSV));
  array_walk($EXP, function(&$_) use ($EXP) {
    $_ = array_combine($EXP[0], $_);
  });
  array_shift($EXP);

  $DEL = array_map('str_getcsv', explode(PHP_EOL, $DEL_CSV));
  array_walk($DEL, function(&$_) use ($DEL) {
    $_ = array_combine($DEL[0], $_);
  });
  array_shift($DEL);

  // remove all .*.au portions of the domain
  foreach ($EXP as $DOMAIN) {
    // end of file line
    if ($DOMAIN['Domain Name'] == '') {
      continue;
    }

    // get the name
    $NAME = substr($DOMAIN['Domain Name'], 0, strpos($DOMAIN['Domain Name'], '.'));

    // check if the domain is a word
    if (pspell_check($DIC, $NAME)) {
      // record if it's a word
      $DOMAINS[] = array(
        'type' => 'expiring',
        'name' => $NAME,
        'domain' => $DOMAIN['Domain Name'],
        'date' => $DOMAIN['Date']
      );
    }
  }

  foreach ($DEL as $DOMAIN) {
    // end of file line
    if ($DOMAIN['Domain Name'] == '') {
      continue;
    }

    // get the name
    $NAME = substr($DOMAIN['Domain Name'], 0, strpos($DOMAIN['Domain Name'], '.'));

    // check if the domain is a word
    if (pspell_check($DIC, $NAME)) {
      // record if it's a word
      $DOMAINS[] = array(
        'type' => 'deleted',
        'name' => $NAME,
        'domain' => $DOMAIN['Domain Name'],
        'date' => $DOMAIN['Date']
      );
    }
  }

  print_r($DOMAINS);

//            [Date] => 2019-11-10
//            [Eligible Purge Time] => 03:32:31
//            [Domain Name] => fastframeless.com.au






?>
