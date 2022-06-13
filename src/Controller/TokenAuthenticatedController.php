<?php

namespace App\Controller;

interface TokenAuthenticatedController
{
    // This interface is implemented by controllers in which all of it's method
    // accept api token as request parameter.

    // For more details check App\EventSubscriber\TokenSubscriber.
}