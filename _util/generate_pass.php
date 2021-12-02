<?php
################################
## Joël Piguet - 2021.11.11 ###
##############################

echo 'Usage: enter list of passwords to get list of encrypted hashed passords' . PHP_EOL;

for ($i = 1; $i < count($argv); $i++) {
    echo password_hash($argv[$i], PASSWORD_BCRYPT) . PHP_EOL;
}
