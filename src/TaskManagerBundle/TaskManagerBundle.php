<?php

namespace TaskManagerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TaskManagerBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
