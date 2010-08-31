<?php

function print_ar($array, $count=0) {
    $i=0;
    $tab ='';
    while($i != $count) {
        $i++;
        $tab .= "&nbsp;&nbsp;|&nbsp;&nbsp;";
    }
    $k=0;
    foreach($array as $key=>$value){
        if(is_array($value)){
            echo $tab."[<strong><u>$key</u></strong>]<br />";
            $count++;
            print_ar($value, $count);
            $count--;
        }
        else{
            $tab2 = substr($tab, 0, -12);
            echo "$tab2~ $key: <strong>$value</strong><br />";
        }
        $k++;
    }
    $count--;
}
?>
