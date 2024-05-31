# Time Logger

## A Time Booking API

## Introduction

This simple and versatile API is designed to help you seamlessly track time bookings for projects and tasks. 

## projects
### Create project
`POST projects/create`

#### Payload

```json
{
  "name": "Acme project"
}
```
#### Result

```
{
    "id": 1,
    "name": "Acme project",
    "tasks": []
}
```

### Get projects data
`GET projects/{projectId}`

#### Result

```
{
    "id": 1,
    "name": "Acme project",
    "tasks": []
}
```

### List projects
`GET projects/list`

#### Result

```
[
    {
        "id": 1,
        "name": "Project 1",
        "tasks": [
            {
                "id": 1,
                "name": "Task 1",
                "bookings": [

                ]
            }
        ]
    },
    {
        "id": 2,
        "name": "Project 2",
        "tasks": [
            {
                "id": 1,
                "name": "Task 1",
                "bookings": [
                    {
                        "description": "Make some code",
                        "key": "018fb158-b384-766a-b48f-2e67f7626662",
                        "start": 1716667331,
                        "end": 1716670352
                    }
                ]
            }
        ]
    }
]
```

### Update project
`PUT projects/{projectId}`

#### Payload

```json
{
  "name": "Acme project"
}
```

### Delete Project
`DELETE projects/{projectId}`

## Tasks
### Create taks
`POST projects/{projectId}/tasks/create`

#### Payload

```json
{
  "name": "Build some code"
}
```

### Get task data
`GET projects/{projectId}/tasks/{taskId}`

### Get tasks list (for a given project)
`GET projects/{projectId}/tasks/list`

### Update task
`PUT projects/{projectId}/tasks/{taskId}`

#### Payload

```json
{
  "name": "New task name"
}
```

### Delete task
`DELETE projects/{projectId}/tasks/{taskId}`

## bookings

### Start booking
`POST projects/{projectId}/tasks/{taskId}/bookings/start`

#### Result
```json
{
  "key": "some-hash-key"
}
```

### Stop booking
`PUT projects/{projectId}/tasks/{taskId}/bookings/stop`

#### Payload

```json
{
  "description": "refactor some code",
  "key": "some-hash-key"
}
```

### List bookings
`GET projects/{projectId}/tasks/{taskId}/bookings/list`

### Get booking data
`GET projects/{projectId}/tasks/{taskId}/booking/{bookingId}`

### Delete booking
`DELETE projects/{projectId}/tasks/{taskId}/booking/{bookingId}`

### Export project bookings
`GET projects/{projectId}/export`

### Export task bookings
`GET projects/{projectId}/task/{taskId}/export`