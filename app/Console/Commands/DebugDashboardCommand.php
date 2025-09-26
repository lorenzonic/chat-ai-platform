<?php

use Illuminate\Database\Eloquent\Model;

class DebugDashboardCommand extends Illuminate\Console\Command
{
    protected $signature = 'debug:dashboard {grower_id=34}';
    protected $description = 'Debug dashboard for grower';

    public function handle()
    {
        $growerId = $this->argument('grower_id');
        $this->info("Testing dashboard for grower ID: $growerId");

        try {
            $grower = \App\Models\Grower::find($growerId);
            if (!$grower) {
                $this->error("Grower $growerId not found!");
                return 1;
            }
            $this->info("✅ Grower found: {$grower->name}");

            // Test query 1
            $totalProducts = $grower->products()->count();
            $this->info("Total Products: $totalProducts");

            // Test query 2
            $productsInOrders = $grower->products()
                ->whereHas('orderItems')
                ->distinct()
                ->count();
            $this->info("Products in Orders: $productsInOrders");

            // Test query 3
            $totalOrders = \App\Models\Order::whereHas('orderItems.product', function($query) use ($grower) {
                $query->where('grower_id', $grower->id);
            })->count();
            $this->info("Total Orders: $totalOrders");

            // Test query 4
            $recentProducts = $grower->products()
                ->with('orderItems')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            $this->info("Recent Products: " . $recentProducts->count());

            $this->info("✅ All queries successful!");

            // Test view data
            $viewData = [
                'grower' => $grower,
                'totalProducts' => $totalProducts,
                'productsInOrders' => $productsInOrders,
                'totalOrders' => $totalOrders,
                'recentProducts' => $recentProducts
            ];

            // Test view rendering
            try {
                $view = view('grower.dashboard', $viewData);
                $this->info("✅ View created successfully");

                $content = $view->render();
                $this->info("✅ View rendered successfully (length: " . strlen($content) . ")");

                return 0;

            } catch (\Exception $e) {
                $this->error("❌ View rendering failed:");
                $this->error($e->getMessage());
                $this->error($e->getFile() . ':' . $e->getLine());
                return 1;
            }

        } catch (\Exception $e) {
            $this->error("❌ General error:");
            $this->error($e->getMessage());
            $this->error($e->getFile() . ':' . $e->getLine());
            return 1;
        }
    }
}
