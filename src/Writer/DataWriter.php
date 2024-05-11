<?php

namespace TimeLogger\Writer;

use TimeLogger\DataTransfers\DataTransferObjects\ProjectTransfer;
use TimeLogger\Service\Config;

class DataWriter
{
    /**
     * @param array<int,ProjectTransfer> $data
     *
     * @return void
     */
    public function write(array $data): void
    {
        $output = [];
        /** @var ProjectTransfer $project */
        foreach ($data as $project) {
            $output[] = $project->toArray(true);
        }

        file_put_contents(Config::getDataFilePath(), json_encode($output, JSON_PRETTY_PRINT));
    }
}
