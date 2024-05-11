# Time Logger

## projects
### Create project
`POST projects/create`

#### Payload

```json
{
  "name": "Acme project"
}
```

### Get projects data
`GET projects/{projectId}`

### List projects
`GET projects/list`

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

### End booking
`PUT projects/{projectId}/tasks/{taskId}/bookings/end`

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

