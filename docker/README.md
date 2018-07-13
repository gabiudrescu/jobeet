## Before you start

Make sure you copy the file ```docker/.env.dist``` to ```docker/.env``` and set the values appropriately.

## Run docker compose

```docker-composer up -d```

## Connect to app container

```docker-composer exec --user application app bash```
