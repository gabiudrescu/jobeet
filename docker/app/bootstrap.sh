#!/bin/bash

groupadd -g "$APPLICATION_GID" "$APPLICATION_GROUP"
useradd -u "$APPLICATION_UID" --home "/home/$APPLICATION_USER" --create-home --shell /bin/bash --no-user-group "$APPLICATION_GROUP" -g "$APPLICATION_GID"

echo "Welcome to Șerpărie!"
