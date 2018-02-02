<?php

    if ($type == 'list')
    {
        echo $inscription->getSummerFunCenter() ? $inscription->getSummerFunCenter() : $inscription->getSummerFunCenterOther();
    }
