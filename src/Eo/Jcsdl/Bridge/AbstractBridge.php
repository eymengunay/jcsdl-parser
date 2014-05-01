<?php

/*
 * This file is part of the Jcsdl package.
 *
 * (c) 2014 Eymen Gunay <eymen@egunay.com>
 */

namespace Eo\Jcsdl\Bridge;

use Eo\Jcsdl\Query;

/**
 * AbstractBridge
 */
abstract class AbstractBridge implements BridgeInterface
{
    /**
     * {@inheritdoc}
     */
    abstract public function transform(Query $query);
}