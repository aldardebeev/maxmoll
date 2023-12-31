<?php

namespace App\Orchid\Layouts\Charts;

use Orchid\Screen\Layouts\Chart;

class DynamicsInterviewedOrders extends Chart
{
    /**
     * Available options:
     * 'bar', 'line',
     * 'pie', 'percentage'.
     *
     * @var string
     */
    protected $type = 'line';

    /**
     * Determines whether to display the export button.
     *
     * @var bool
     */
    protected $export = true;

    protected $target = 'DynamicsInterviewedOrders';

    protected $title = 'Завершенные заказы';
}
