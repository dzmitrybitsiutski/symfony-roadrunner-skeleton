<?php

declare(strict_types=1);

namespace App\RoadrunnerBundle;

if (!\function_exists('App\RoadrunnerBundle\consumes')) {
    /**
     * @internal
     */
    function consumes(\Iterator $gen): void
    {
        foreach ($gen as $_) {
        }
    }
}
