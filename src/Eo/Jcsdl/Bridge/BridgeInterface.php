<?php

/*
 * This file is part of the Jcsdl package.
 *
 * (c) 2014 Eymen Gunay <eymen@egunay.com>
 */

namespace Eo\Jcsdl\Bridge;

use Eo\Jcsdl\Query;

/**
 * BridgeInterface
 */
interface BridgeInterface
{
    /**
     * Transform query
     *
     * @param  Query $query
     * @return mixed
     */
    public function transform(Query $query);
}