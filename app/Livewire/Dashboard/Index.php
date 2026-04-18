<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardService;
use Livewire\Component;

class Index extends Component
{
    public array $metrics = [];
    public array $lowStockAlerts = [];

    public function mount()
    {
        $service = new DashboardService();
        $user = auth()->user();

        $deptIds = $user->getAccessibleDepartmentIds();
        $catIds = $user->getAccessibleCategoryIds();

        $this->metrics = $service->getMetrics($deptIds, $catIds);
        $this->lowStockAlerts = $service->getLowStockAlerts($deptIds, $catIds)->toArray();
    }

    public function render()
    {
        return view('livewire.dashboard.index')
            ->layout('layouts.app', ['title' => 'Dashboard']);
    }
}
