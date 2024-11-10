<?php

namespace cjrasmussen\BlueskyApi\Traits\Traits;

use cjrasmussen\BlueskyApi\Traits\ServerRequests;

trait Authentication
{
    use ServerRequests;
    private object $activeSession;
}