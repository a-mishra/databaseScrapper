<?php

    $str = "this is a 1234542398676545 16digit number 987645672345098700
    thissis line  2 of thew same string 87873388837336t36766272828828783288728278278282
    linr 3865884467444847644746474657 85785856474647 47477575757674  47474747474657564575657485";

    $matches = array();
    preg_match_all('/[0-9]{16}+/', $str, $matches);

    //print_r($matches);

    if ( count($matches) ) {
        $shouldUpdate = true;
        $matchedValues = $matches[0];
        $replacementValues = array();
        for ($i =0 ; $i<count($matchedValues); $i++) {
            $replacementValues[$i] = hash16digit($matchedValues[$i]);
        }
        print_r($matchedValues);
        print_r($replacementValues);

        $str = str_replace($matchedValues, $replacementValues, $str);
    }

    /* output : this is a 1234********6545 16digit number 9876********098700
    thissis line  2 of thew same string 87873388837336t3676********28783288********8282
    linr 3865********4847644746474657 85785856474647 47477575757674  4747********57564575657485
    */
echo $str;

//-- iNline function  -----------------

/** This function will hash the 16 digit nnumber */
function hash16digit($input16DigitNumber) {
$returnString = $input16DigitNumber;

    //cross check length of string
    if(strlen($input16DigitNumber) == 16) {
        $returnString = substr($input16DigitNumber, 0, 4)."********".substr($input16DigitNumber, -4);
    }

    return $returnString;
}



?>