<?php

declare(strict_types=1);

namespace App\Modules\Orders\Infrastructure;

use App\Modules\Orders\Domain\ActiveOrder;

/**
 * @extends EloquentOrderRepository<ActiveOrder>
 */
class EloquentActiveOrderRepository extends EloquentOrderRepository implements ActiveOrderRepository {}
