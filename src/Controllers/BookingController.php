<?php

namespace TimeLogger\Controllers;

use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;
use TimeLogger\DataTransfers\DataTransferObjects\BookingTransfer;
use TimeLogger\Reader\DataReader;
use TimeLogger\Writer\DataWriter;

class BookingController
{
    public function __construct(
        private DataReader $dataReader,
        private DataWriter $dataWriter,
    ) {
    }

    public function start(string $projectId, string $taskId): JsonResponse
    {
        $projectId = (int)$projectId;
        $taskId = (int)$taskId;

        $data = $this->dataReader->read();

        foreach ($data as $projectTransfer) {
            if ($projectTransfer->getId() === $projectId) {
                foreach ($projectTransfer->getTasks() as $taskTransfer) {
                    if ($taskTransfer->getId() === $taskId) {
                        $key = Uuid::v7()->toRfc4122();
                        $bookingTransfer = new BookingTransfer();
                        $bookingTransfer->setStart(time())
                            ->setKey($key);
                        $taskTransfer->addBooking($bookingTransfer);
                        $this->dataWriter->write($data);

                        return new JsonResponse(['key' => $key]);
                    }
                }
            }
        }

        return new JsonResponse(['error' => 'Project not found'], 404);
    }

    public function end(Request $request, string $projectId, string $taskId): JsonResponse
    {
        $projectId = (int)$projectId;
        $taskId = (int)$taskId;
        $parameters = json_decode($request->getContent(), true);
        $description = $parameters['description'];
        $key = $parameters['key'];

        $data = $this->dataReader->read();

        foreach ($data as $projectTransfer) {
            if ($projectTransfer->getId() === $projectId) {
                foreach ($projectTransfer->getTasks() as $taskTransfer) {
                    if ($taskTransfer->getId() === $taskId) {
                        foreach ($taskTransfer->getBookings() as $bookingTransfer) {
                            if ($bookingTransfer->getKey() === $key) {
                                $bookingTransfer
                                    ->setEnd(time())
                                    ->setDescription($description);
                                $this->dataWriter->write($data);

                                return new JsonResponse($projectTransfer->toArray(true));
                            }
                        }
                    }
                }

                break;
            }
        }

        return new JsonResponse(['error' => 'Project not found'], 404);
    }

    public function read(string $projectId, string $taskId): JsonResponse
    {
        throw new Exception('Not implemented');
    }

    public function list(string $projectId): JsonResponse
    {
        throw new Exception('Not implemented');
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
