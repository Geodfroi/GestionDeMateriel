<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.12 ###
##############################
//unused code potentially useful later.

namespace app\helpers\db;

// sort non associative array of custom types.
// usort($articles, array($this, "_compareExpired"));

// /**
//  * sort results so that articles past the expiration date are pushed at the bottom.
//  */
// function _compareExpired(Article $a, Article $b): int
// {
//     $delta_a = Util::getDaysUntil($a->getExpirationDate());
//     $delta_b = Util::getDaysUntil($b->getExpirationDate());
//     return   $delta_a >    $delta_b;

//     // if ($delta_a > 0 && $delta_b < 0) {
//     //     return 1;
//     // }
//     // if ($delta_a < 0 && $delta_b > 0) {
//     //     return -1;
//     // }
//     // return 0;
// }
