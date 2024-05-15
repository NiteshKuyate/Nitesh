<?php
/**
 * @author Nitesh Kuyate
 * @copyright All rights reserved.
 */

declare(strict_types=1);

namespace Nitesh\CustomerImports\Model;
 
use Magento\Framework\Exception;
use Magento\Framework\Filesystem\Io\File;
use Magento\Store\Model\StoreManagerInterface;
use Nitesh\CustomerImports\Model\Import\CustomerImport;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Exception\NoSuchEntityException;
 
class Customer
{
    /**
     * @var OutputInterface
     */
    private $output;
    
    /**
     * Customer Creation constructor
     *
     * @param File $file
     * @param StoreManagerInterface $storeManagerInterface
     * @param CustomerImport $customerImport
     */
    public function __construct(
        private File $file,
        private StoreManagerInterface $storeManagerInterface,
        private CustomerImport $customerImport
    ) {
    }

    /**
     * Create customer
     *
     * @param array $data
     * @param int $websiteId
     * @param int $storeId
     * @throws NoSuchEntityException
     */
    public function createCustomer(array $data, int $websiteId, int $storeId): void
    {
        try {

            /**
             * Collect customer data
             */
            $customerData = [
                'email'         => $data['emailaddress'],
                '_website'      => 'base',
                '_store'        => 'default',
                'confirmation'  => null,
                'dob'           => null,
                'firstname'     => $data['fname'],
                'gender'        => null,
                'lastname'      => $data['lname'],
                'middlename'    => null,
                'prefix'        => null,
                'store_id'      => $storeId,
                'website_id'    => $websiteId,
                'password'      => null,
                'disable_auto_group_change' => 0
            ];
            
            /**
             * Import customer data
             */
            $this->customerImport->importCustomerData($customerData);
        } catch (Exception $e) {
            $this->output->writeln(
                '<error>'. $e->getMessage() .'</error>',
                OutputInterface::OUTPUT_NORMAL
            );
        }
    }
}
