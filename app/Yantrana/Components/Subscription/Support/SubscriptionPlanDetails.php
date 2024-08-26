<?php

namespace App\Yantrana\Components\Subscription\Support;

use ArrayObject;

/**
 * Subscription Plan details Response class
 */
class SubscriptionPlanDetails extends ArrayObject
{
    // public $has_active_plan;

    public function __construct($array = [])
    {
        parent::__construct($array, ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * Check if the vendor has active plan or not
     *
     * @return bool
     */
    public function hasActivePlan()
    {
        return $this->has_active_plan ?? null;
    }

    public function planType()
    {
        return $this->plan_type ?? null;
    }
    public function currentUsage()
    {
        return $this->current_usage ?? null;
    }
    public function isLimitAvailable()
    {
        return $this->is_limit_available ?? null;
    }
    public function planTitle()
    {
        return $this->plan_title ?? null;
    }
    public function isAuto()
    {
        return $this->subscription_type == 'auto';
    }
}
