.PHONY: all

docker-exec = cd .infra && docker-compose exec record-api

start:
	@echo "=== Starting the Docker environment ==="
	cd .infra && docker-compose up -d

stop:
	@echo "=== Stopping the Docker environment ==="
	cd .infra && docker-compose down

shell:
	@echo "=== Opening a shell ==="
	${docker-exec} sh
