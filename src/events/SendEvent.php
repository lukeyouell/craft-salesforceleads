<?php

namespace lukeyouell\salesforceleads\events;

use yii\base\Event;

class SendEvent extends Event
{
    public $submission;

    public $isSpam = false;
}
