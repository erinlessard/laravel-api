## Setting up the Project
This project makes use of [Laravel Sail.](https://laravel.com/docs/10.x/sail)

### Docker setup
#### MacOS
1. Install [Docker Desktop](https://www.docker.com/products/docker-desktop/)
2. Ensure that your Docker is able to run containers (`docker run -d -p 80:80 docker/getting-started`)

#### Linux
1. Install [Docker Desktop](https://www.docker.com/products/docker-desktop/)
2. In a shell, execute `docker context use default`
3. Ensure that your Docker is able to run containers (`docker run -d -p 80:80 docker/getting-started`)

#### Windows
1. Install [Docker Desktop](https://www.docker.com/products/docker-desktop/)
2. Ensure that Windows Subsystem for Linux 2 (WSL2) is installed and enabled.
   - After installing and enabling WSL2, ensure that Docker Desktop is [configured to use the WSL2 backend.](https://docs.docker.com/docker-for-windows/wsl/)
3. Ensure that your Docker is able to run containers (`docker run -d -p 80:80 docker/getting-started`)

See [https://laravel.com/docs/10.x/installation#docker-installation-using-sail](https://laravel.com/docs/10.x/installation#docker-installation-using-sail) for official Laravel instructions and troubleshooting.

After setting up docker and ensuring the host machine can run docker containers, change to the project directory and execute a composer install through Docker to install Sail:
```
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

Afterwards, copy the .env file and make any changes
```
cp .env.example .env
```

Finally, run migrations
```
vendor/bin/sail artisan migrate
```
## Running
To run the containers for development work, change to the project directory:
```
vendor/bin/sail up -d
```
To view available sail commands:
```
vendor/bin/sail
```
To run tests:
```
vendor/bin/sail test
```

## API Endpoints

```
POST /api/jobs
```
Creates a new job with the provided JSON payload

```
text: required, min 1, max 2000
tasks: required, array containing one or more of: call_reason, call_actions, call_segments, summary, satisfaction

Example JSON:
{
    "text": "blah",
    "tasks": [
        "call_reason", 
        "call_actions",
        "call_segments",
        "summary",
        "satisfaction"
    ]
}
```

Returns 200 containing ULID of new Job if successful

Example Response: 
```
01hre7sx0rwqv2fwqn3stg66g6
```
---

```
GET /api/jobs/{jobId}
```
Returns a Job by ID and any Tasks assigned to it.
```
jobId: required, ULID of a job

Example request: /api/jobs/01hre7sx0rwqv2fwqn3stg66g6
```
Example Response:

```
{
  "id": "01hre7wyg0z1wvdernw454djz4",
  "text": "a long text string",
  "created_at": "2024-03-08T05:17:14.000000Z",
  "updated_at": "2024-03-08T05:17:14.000000Z",
  "tasks": [
    {
      "id": "01hre7wyg76dcmqts9ht72ack5",
      "job_id": "01hre7wyg0z1wvdernw454djz4",
      "type": "call_reason",
      "processed": 1,
      "result": {
        "result": "this was a reasonable call"
      },
      "created_at": "2024-03-08T05:17:14.000000Z",
      "updated_at": "2024-03-08T05:17:14.000000Z"
    },
    {
      "id": "01hre7wyg9kdgvftdkv3shkaae",
      "job_id": "01hre7wyg0z1wvdernw454djz4",
      "type": "call_actions",
      "processed": 1,
      "result": {
        "result": "successfully processed call action"
      },
      "created_at": "2024-03-08T05:17:14.000000Z",
      "updated_at": "2024-03-08T05:17:14.000000Z"
    },
    {
      "id": "01hre7wygaqsv5g7qt0rptqpvk",
      "job_id": "01hre7wyg0z1wvdernw454djz4",
      "type": "call_segments",
      "processed": 1,
      "result": null,
      "created_at": "2024-03-08T05:17:14.000000Z",
      "updated_at": "2024-03-08T05:17:14.000000Z"
    },
    {
      "id": "01hre7wygc7z0h07z7hrk103zt",
      "job_id": "01hre7wyg0z1wvdernw454djz4",
      "type": "satisfaction",
      "processed": 1,
      "result": {
        "result": "5/10"
      },
      "created_at": "2024-03-08T05:17:14.000000Z",
      "updated_at": "2024-03-08T05:17:14.000000Z"
    },
    {
      "id": "01hre7wygdt0vsbw39ehf24xwk",
      "job_id": "01hre7wyg0z1wvdernw454djz4",
      "type": "summary",
      "processed": 1,
      "result": {
        "result": "a long summary result"
      },
      "created_at": "2024-03-08T05:17:14.000000Z",
      "updated_at": "2024-03-08T05:17:14.000000Z"
    }
  ]
}
```
