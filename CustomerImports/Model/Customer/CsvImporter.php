<?php
/**
 * @author Nitesh Kuyate
 * @copyright All rights reserved.
 */

declare(strict_types=1);

namespace Nitesh\CustomerImports\Model\Customer;

use Nitesh\CustomerImports\Api\ImportInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem\Driver\File;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;

class CsvImporter implements ImportInterface
{
    /**
     * @var []
     */
    protected $keys;

    /**
     * Csv Importer constructor
     *
     * @param File $file
     * @param Csv $csv
     * @param LoggerInterface $logger
     */
    public function __construct(
        private File $file,
        protected Csv $csv,
        private LoggerInterface $logger
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getImportData(InputInterface $input): array
    {
        $file = $input->getArgument(ImportInterface::FILE_PATH);
        return $this->readData($file);
    }

    /**
     * @inheritDoc
     */
    public function readData(string $file): array
    {
        try {
            if (!$this->file->isExists($file)) {
                throw new LocalizedException(__('Invalid file path or no file found.'));
            }
            $this->csv->setDelimiter(",");
            $data = $this->csv->getData($file);
            $this->logger->info('CSV file is parsed');
        } catch (FileSystemException $e) {
            $this->logger->info($e->getMessage());
            throw new LocalizedException(__('File system exception' . $e->getMessage()));
        }

        return $this->formatData($data);
    }

    /**
     * @inheritDoc
     */
    public function formatData($data): array
    {
        $this->keys = array_shift($data);
        array_walk($data, function (&$v) {
            $v = array_combine($this->keys, $v);
        });

        return $data;
    }
}
