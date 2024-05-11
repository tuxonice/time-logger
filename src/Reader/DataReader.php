<?php

namespace TimeLogger\Reader;

use TimeLogger\DataTransfers\DataTransferObjects\BookingTransfer;
use TimeLogger\DataTransfers\DataTransferObjects\ProjectTransfer;
use TimeLogger\DataTransfers\DataTransferObjects\TaskTransfer;
use TimeLogger\Service\Config;

class DataReader
{
    /**
     * @var array<mixed>
     */
    private array $data = [];

    /**
     * @return array<int,ProjectTransfer>
     */
    public function read(): array
    {
        if (empty($this->data)) {
            $this->data = json_decode(file_get_contents(Config::getDataFilePath()), true);
        }

        $structure = [];
        foreach ($this->data as $project) {
            $projectTransfer = new ProjectTransfer();
            $projectTransfer
                ->setName($project['name'])
                ->setId($project['id']);
            foreach ($project['tasks'] as $task) {
                $taskTransfer = new TaskTransfer();
                $taskTransfer
                    ->setId($task['id'])
                    ->setName($task['name']);
                foreach ($task['bookings'] as $booking) {
                    $bookingTransfer = new BookingTransfer();
                    $bookingTransfer->setKey($booking['key']);
                    $bookingTransfer->setDescription($booking['description']);
                    $bookingTransfer->setStart($booking['start']);
                    $bookingTransfer->setEnd($booking['end']);
                    $taskTransfer->addBooking($bookingTransfer);
                }

                $projectTransfer->addTask($taskTransfer);
            }

            $structure[] = $projectTransfer;
        }

        return $structure;
    }

    public function getNextProjectId(): int
    {
        if (empty($this->data)) {
            $this->data = json_decode(file_get_contents(Config::getDataFilePath()), true);
        }

        $projectIds = [];
        foreach ($this->data as $project) {
            $projectIds[] = (int)$project['id'];
        }

        return !empty($projectIds) ? max($projectIds) + 1 : 1;
    }

    public function getNextTaskIdForProject(int $projectId): int
    {
        if (empty($this->data)) {
            $this->data = json_decode(file_get_contents(Config::getDataFilePath()), true);
        }

        $taskIds = [];
        foreach ($this->data as $project) {
            if ((int)$project['id'] === $projectId) {
                foreach ($project['tasks'] as $task) {
                    $taskIds[] = (int)$task['id'];
                }
            };
        }

        return !empty($taskIds) ? max($taskIds) + 1 : 1;
    }
}
