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
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;

class JsonImporter implements ImportInterface
{
    /**
     * CsvImporter constructor
     *
     * @param File $file
     * @param SerializerInterface $serializer
     * @param LoggerInterface $logger
     */
    public function __construct(
        private File $file,
        private SerializerInterface $serializer,
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
            $data = $this->file->fileGetContents($file);
            $this->logger->info('JSON file is parsed');
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
        return $this->serializer->unserialize($data);
    }
}
