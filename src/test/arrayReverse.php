<?php

$input = array("PHP", 4.0, array(1, 2));
$reversed = array_reverse($input);
$preserved = array_reverse($input, true);

print_r($input);
print_r($reversed);
print_r($preserved);
