<?php
/**
 * @author Nitesh Kuyate
 * @copyright All rights reserved.
 */

declare(strict_types=1);

namespace Nitesh\CustomerImports\Model\Customer;

use Nitesh\CustomerImports\Api\ImportInterface;
use Magento\Framework\ObjectManagerInterface;

class ProfileFactory
{
    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        protected ObjectManagerInterface $objectManager
    ) {
    }

    /**
     * Create class instance with specified parameters
     *
     * @param string $type
     * @throws \Exception
     */
    public function create(string $type): ImportInterface
    {
        if ($type === "csv") {
            $class = CsvImporter::class;
        } elseif ($type === "json") {
            $class = JsonImporter::class;
        } else {
            throw new \Exception("The specified profile type is not supported.");
        }
        return $this->objectManager->create($class);
    }
}
