.PHONY: all

start:
	@echo "=== Starting the Docker environment ==="
	cd .infra && docker-compose up -d

stop:
	@echo "=== Stopping the Docker environment ==="
	cd .infra && docker-compose down
