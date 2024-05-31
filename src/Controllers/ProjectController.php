<?php

namespace TimeLogger\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use TimeLogger\DataTransfers\DataTransferObjects\ProjectTransfer;
use TimeLogger\Reader\DataReader;
use TimeLogger\Writer\DataWriter;

class ProjectController
{
    public function __construct(
        private DataReader $dataReader,
        private DataWriter $dataWriter,
    ) {
    }

    public function create(Request $request): JsonResponse
    {
        $parameters = json_decode($request->getContent(), true);
        $name = $parameters['name'];

        $data = $this->dataReader->read();

        $nextProjectId = $this->dataReader->getNextProjectId();

        $projectTransfer = new ProjectTransfer();
        $projectTransfer->setName($name)
            ->setId($nextProjectId);

        $data[] = $projectTransfer;
        $this->dataWriter->write($data);

        return new JsonResponse($projectTransfer->toArray(true));
    }

    public function read(string $projectId): JsonResponse
    {
        $projectId = (int)$projectId;
        $data = $this->dataReader->read();

        $projectData = array_filter($data, function (ProjectTransfer $projectTransfer) use ($projectId) {
            return $projectTransfer->getId() === $projectId;
        });

        if (empty($projectData)) {
            return new JsonResponse(['error' => 'Project not found'], 404);
        }

        return new JsonResponse(current($projectData)->toArray(true));
    }

    public function list(): JsonResponse
    {
        $data = $this->dataReader->read();

        $output = [];
        foreach ($data as $project) {
            $output[] = $project->toArray(true);
        }

        return new JsonResponse($output);
    }

    public function export(string $projectId): StreamedResponse
    {
        $projectId = (int)$projectId;

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

    public function update(Request $request, string $projectId): JsonResponse
    {
        $projectId = (int)$projectId;

        $parameters = json_decode($request->getContent(), true);
        $name = $parameters['name'];

        $data = $this->dataReader->read();

        foreach ($data as $projectTransfer) {
            if ($projectTransfer->getId() === $projectId) {
                $projectTransfer->setName($name);
                $this->dataWriter->write($data);
                return new JsonResponse($projectTransfer->toArray(true));
            }
        }

        return new JsonResponse(['error' => 'Project not found'], 404);
    }

    public function delete(string $projectId): JsonResponse
    {
        $projectId = (int)$projectId;

        $data = $this->dataReader->read();

        foreach ($data as $key => $projectTransfer) {
            if ($projectTransfer->getId() === $projectId) {
                unset($data[$key]);
                $this->dataWriter->write($data);

                return new JsonResponse($projectTransfer->toArray(true));
            }
        }

        return new JsonResponse(['error' => 'Project not found'], 404);
    }
}
