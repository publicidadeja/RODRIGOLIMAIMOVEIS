<?php

namespace Srapid\Theme\Events;

use Srapid\Base\Events\Event;
use Illuminate\Queue\SerializesModels;

class RenderingHomePageEvent extends Event
{
    use SerializesModels;
}