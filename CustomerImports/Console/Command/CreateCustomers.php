<?php
/**
 * @author Nitesh Kuyate
 * @copyright All rights reserved.
 */

declare(strict_types=1);

namespace Nitesh\CustomerImports\Console\Command;

use Magento\Framework\Console\Cli;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Filesystem;
use Magento\Framework\App\State;
use Magento\Framework\App\Area;
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Nitesh\CustomerImports\Api\ImportInterface;
use Nitesh\CustomerImports\Model\Customer\ProfileFactory;
use Nitesh\CustomerImports\Model\Customer;

class CreateCustomers extends Command
{
    /**
     * @var ImportInterface
     */
    protected $importer;

    /**
     * Customer Imports constructor.
     *
     * @param ProfileFactory $profileFactory
     * @param Customer $customer
     * @param StoreManagerInterface $storeManager
     * @param Filesystem $filesystem
     * @param State $state
     */
    public function __construct(
        protected ProfileFactory $profileFactory,
        private Customer $customer,
        protected StoreManagerInterface $storeManager,
        private Filesystem $filesystem,
        private State $state
    ) {
        parent::__construct();
    }

    /**
    * {@inheritdoc}
    */
    protected function configure(): void
    {
        $this->setName("customer:import");
        $this->setDescription("Customer Imports via CSV & JSON");
        $this->setDefinition([
            new InputArgument(
                ImportInterface::PROFILE_NAME,
                InputArgument::REQUIRED,
                "Profile name ex: sample-csv/sample-json"),
            new InputArgument(
                ImportInterface::FILE_PATH,
                InputArgument::REQUIRED,
                "File Path ex: sample.csv/sample.json")
        ]);
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output):int
    {
        $profileType = $input->getArgument(ImportInterface::PROFILE_NAME);
        $filePath = $input->getArgument(ImportInterface::FILE_PATH);
        $output->writeln(sprintf("Profile type: %s", $profileType));
        $output->writeln(sprintf("File Path: %s", $filePath));

        try {
            $this->state->setAreaCode(Area::AREA_GLOBAL);

            if ($importData = $this->getImporterInstance($profileType)->getImportData($input)) {
                $storeId = $this->storeManager->getStore()->getId();
                $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
                
                foreach ($importData as $data) {
                    $this->customer->createCustomer($data, $websiteId, $storeId);
                }

                $output->writeln(sprintf(
                    "Total of %s Customers are imported",
                    count($importData)));
                return Cli::RETURN_SUCCESS;
            }

            return Cli::RETURN_FAILURE;
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $output->writeln("<error>$msg</error>", OutputInterface::OUTPUT_NORMAL);
            return Cli::RETURN_FAILURE;
        }
    }

    /**
     * Get importer instance
     *
     * @param string $profileType
     * @return ImportInterface
     */
    protected function getImporterInstance(string $profileType): ImportInterface
    {
        if (!($this->importer instanceof ImportInterface)) {
            $this->importer = $this->profileFactory->create($profileType);
        }
        return $this->importer;
    }
}
