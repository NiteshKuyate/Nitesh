<?php
/**
 * @author Nitesh Kuyate
 * @copyright All rights reserved.
 */

declare(strict_types=1);

namespace Nitesh\CustomerImports\Api;

use Symfony\Component\Console\Input\InputInterface;

interface ImportInterface
{
    /**
     * Profile name constant
     */
    public const PROFILE_NAME = "profile";
    /**
     * File path constant
     */
    public const FILE_PATH = "filepath";

    /**
     * Get the import data
     *
     * @param InputInterface $input
     * @return array
     */
    public function getImportData(InputInterface $input): array;

    /**
     * Read the data
     *
     * @param string $data
     * @return array
     */
    public function readData(string $data): array;

    /**
     * Format the data
     *
     * @param mixed $data
     * @return array
     */
    public function formatData($data): array;
}
