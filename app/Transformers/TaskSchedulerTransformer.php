<?php
/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */

namespace App\Transformers;


use App\Models\ScheduledJob;
use App\Models\Scheduler;
use App\Utils\Traits\MakesHash;

class TaskSchedulerTransformer extends EntityTransformer
{
    use MakesHash;

    protected $defaultIncludes = [
        'job'
    ];

    public function includeJob(Scheduler $scheduler)
    {
        $transformer = new ScheduledJobTransformer($this->serializer);

        return $this->item($scheduler->job, $transformer, ScheduledJob::class);
    }

    public function transform(Scheduler $scheduler)
    {
        return [
            'id' => $this->encodePrimaryKey($scheduler->id),
            'paused' => (bool)$scheduler->paused,
            'repeat_every' => (string)$scheduler->repeat_every,
            'start_from' => (int)$scheduler->start_from,
            'scheduled_run' => (int)$scheduler->scheduled_run,
            'updated_at' => (int)$scheduler->updated_at,
            'created_at' => (int)$scheduler->created_at,
            'archived_at' => (int) $scheduler->deleted_at,
        ];
    }

}
