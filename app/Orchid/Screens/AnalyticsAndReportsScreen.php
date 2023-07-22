<?php

namespace App\Orchid\Screens;

use App\Models\Order;
use App\Orchid\Layouts\Charts\DynamicsInterviewedOrders;
use App\Orchid\Layouts\Charts\ReportCompletedTime;
use Orchid\Screen\Screen;

class AnalyticsAndReportsScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'reportCompletedTime' => Order::countForGroup('status')->toChart(),
            'DynamicsInterviewedOrders' => [
                Order::where('status', 'completed')->countByDays(null, null, 'updated_at')->toChart('Завершенные заказы'),
            ]
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Аналитика и отчеты';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            ReportCompletedTime::class,
            DynamicsInterviewedOrders::class
        ];
    }
}
