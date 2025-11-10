kafka-create-topics:
	./vendor/bin/sail exec kafka bash -lc "kafka-topics --bootstrap-server kafka:9092 --create --if-not-exists --topic tasks.status.updated --replication-factor 1 --partitions 1"
	./vendor/bin/sail exec kafka bash -lc "kafka-topics --bootstrap-server kafka:9092 --create --if-not-exists --topic comments.created --replication-factor 1 --partitions 1"

test:
	yarn build
	yarn playwright install
	php artisan config:clear --env=testing
	php artisan test --coverage --parallel
# 	php artisan test --parallel --group=feature

lint:
	make ide
	./vendor/bin/phpstan analyse
	./vendor/bin/rector process --ansi
	./vendor/bin/pint --parallel
	yarn lint

ide:
	php artisan ide-helper:generate
	php artisan ide-helper:models -RW
	php artisan ide-helper:meta
