.PHONY: all

docker-exec = cd .infra && docker-compose exec record-api
console = php bin/console

start:
	@echo "=== Starting the Docker environment ==="
	cd .infra && docker-compose up -d

stop:
	@echo "=== Stopping the Docker environment ==="
	cd .infra && docker-compose down

shell:
	@echo "=== Opening a shell ==="
	${docker-exec} sh

migrate:
	@echo "=== Running Doctrine migrations ==="
	${docker-exec} ${console} doctrine:migrations:migrate

populate:
	@echo "=== Running data fixtures ==="
	${docker-exec} ${console} hautelook:fixtures:load --no-bundles

phpunit:
	@echo "=== Running unit tests ==="
	${docker-exec} php bin/phpunit
