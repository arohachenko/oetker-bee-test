.PHONY: all

info:
	@echo "Available commands:"
	@echo ""
	@echo "Command                              Description"
	@echo "-------                              -----------"
	@echo "make info                            Show the available make commands"
	@echo "make start                           Start the Docker environment"
	@echo "make stop                            Stop the Docker environment"
	@echo "make status                          Show the status of the Docker environment"
	@echo "make phpunit                         Run all the tests there are"
	@echo "make shell                           Open a shell in the php-fpm container"
	@echo "make populate                        Prefill database with sample data"
	@echo ""

docker-exec = cd .infra && docker-compose exec record-api
console = php bin/console

start:
	@echo "=== Starting the Docker environment ==="
	cd .infra && docker-compose up -d

stop:
	@echo "=== Stopping the Docker environment ==="
	cd .infra && docker-compose down

status:
	@echo "=== Status of the Docker environment ==="
	cd .infra && docker-compose ps

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
