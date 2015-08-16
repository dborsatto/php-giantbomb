<?php

/**
 * This file is part of the GiantBomb PHP API created by Davide Borsatto.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright (c) 2015, Davide Borsatto
 */
namespace dborsatto\GiantBomb;

/**
 * Interface ConfigInterface.
 *
 * @author Davide Borsatto <davide.borsatto@gmail.com>
 */
interface ConfigInterface
{
    /**
     * Returns the API key.
     *
     * @return string
     */
    public function getApiKey();

    /**
     * Returns the API endpoint.
     *
     * @return string
     */
    public function getApiEndpoint();

    /**
     * Returns the repositoy configuration.
     *
     * @return array
     */
    public function getRepositories();
}
