<?php

namespace TimeLogger\Controllers;

use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use TimeLogger\DataTransfers\DataTransferObjects\ProjectTransfer;
use TimeLogger\DataTransfers\DataTransferObjects\TaskTransfer;
use TimeLogger\Reader\DataReader;
use TimeLogger\Writer\DataWriter;

class TaskController
{
    public function __construct(
        private DataReader $dataReader,
        private DataWriter $dataWriter,
    ) {
    }

    public function create(Request $request, string $projectId): JsonResponse
    {
        $projectId = (int)$projectId;
        $parameters = json_decode($request->getContent(), true);
        $name = $parameters['name'];

        $data = $this->dataReader->read();

        $nextTaskId = $this->dataReader->getNextTaskIdForProject($projectId);

        foreach ($data as $projectTransfer) {
            if ($projectTransfer->getId() === $projectId) {
                $taskTransfer = new TaskTransfer();
                $taskTransfer
                    ->setName($name)
                    ->setId($nextTaskId);
                $projectTransfer->addTask($taskTransfer);
                $this->dataWriter->write($data);

                return new JsonResponse($projectTransfer->toArray(true));
            }
        }

        return new JsonResponse(['error' => 'Project not found'], 404);
    }

    public function read(string $projectId, string $taskId): JsonResponse
    {
        $projectId = (int)$projectId;
        $taskId = (int)$taskId;
        $data = $this->dataReader->read();

        $projectData = array_filter($data, function (ProjectTransfer $projectTransfer) use ($projectId) {
            return $projectTransfer->getId() === $projectId;
        });

        if (empty($projectData)) {
            return new JsonResponse(['error' => 'Project not found'], 404);
        }

        $projectTransfer = current($projectData);

        $taskData = array_filter($projectTransfer->getTasks(), function (TaskTransfer $taskTransfer) use ($taskId) {
            return $taskTransfer->getId() === $taskId;
        });

        if (empty($taskData)) {
            return new JsonResponse(['error' => 'Task not found'], 404);
        }

        return new JsonResponse(current($taskData)->toArray(true));
    }

    public function list(string $projectId): JsonResponse
    {
        $projectId = (int)$projectId;
        $data = $this->dataReader->read();

        $projectData = array_filter($data, function (ProjectTransfer $projectTransfer) use ($projectId) {
            return $projectTransfer->getId() === $projectId;
        });

        if (empty($projectData)) {
            return new JsonResponse(['error' => 'Project not found'], 404);
        }

        $projectTransfer = current($projectData);

        $output = [];
        foreach ($projectTransfer->getTasks() as $task) {
            $output[] = $task->toArray(true);
        }

        return new JsonResponse($output);
    }

    public function export(string $projectId, string $taskId): StreamedResponse
    {
        $projectId = (int)$projectId;
        $taskId = (int)$taskId;

        $data = $this->dataReader->read();

        $header = [
            'project',
            'task',
            'description',
            'start',
            'stop',
            'duration',
        ];
        $bookings = [];
        foreach ($data as $projectTransfer) {
            if ($projectTransfer->getId() === $projectId) {
                foreach ($projectTransfer->getTasks() as $taskTransfer) {
                    if ($taskTransfer->getId() === $taskId) {
                        foreach ($taskTransfer->getBookings() as $bookingTransfer) {
                            $bookings[] = [
                                'project' => $projectTransfer->getName(),
                                'task' => $taskTransfer->getName(),
                                'description' => $bookingTransfer->getDescription(),
                                'start' => date('c', $bookingTransfer->getStart()),
                                'stop' => date('c',$bookingTransfer->getEnd()),
                                'duration' => round(($bookingTransfer->getEnd() - $bookingTransfer->getStart()) / (60 * 60),2)
                            ];
                        }
                    }
                }
                break;
            }
        }

        return new StreamedResponse(
            function () use ($bookings, $header) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, $header, ";");
                foreach ($bookings as $row) {
                    fputcsv($handle, $row, ";");
                }
                fclose($handle);
            },
            200,
            [
                'Content-type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=bookings.csv',
            ]
        );
    }

    public function update(Request $request, string $projectId, string $taskId): JsonResponse
    {
        throw new Exception('Not implemented');
    }

    public function delete(string $projectId, string $taskId): JsonResponse
    {
        throw new Exception('Not implemented');
    }
}
