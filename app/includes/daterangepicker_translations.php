<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

/* Translations for the datepicker external library */
return [
    'direction' => language()->direction,
    'format' => 'YYYY-MM-DD',
    'separator' => ' - ',
    'applyLabel' => language()->global->date->apply,
    'cancelLabel' => language()->global->date->cancel,
    'fromLabel' => language()->global->date->from,
    'toLabel' => language()->global->date->to,
    'customRangeLabel' => language()->global->date->custom,
    'weekLabel' => 'W',
    'daysOfWeek' => [
        language()->global->date->short_days->{7},
        language()->global->date->short_days->{1},
        language()->global->date->short_days->{2},
        language()->global->date->short_days->{3},
        language()->global->date->short_days->{4},
        language()->global->date->short_days->{5},
        language()->global->date->short_days->{6}
    ],
    'monthNames' => [
        language()->global->date->long_months->{1},
        language()->global->date->long_months->{2},
        language()->global->date->long_months->{3},
        language()->global->date->long_months->{4},
        language()->global->date->long_months->{5},
        language()->global->date->long_months->{6},
        language()->global->date->long_months->{7},
        language()->global->date->long_months->{8},
        language()->global->date->long_months->{9},
        language()->global->date->long_months->{10},
        language()->global->date->long_months->{11},
        language()->global->date->long_months->{12},
    ],
    'firstDay' => 1
];
