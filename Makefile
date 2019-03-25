.PHONY: up start exec

up:
	docker-compose up -d --force-recreate

start: up
	sleep 5
	docker-compose exec fpm php bin/console doctrine:query:sql "alter user 'root'@'%'identified with mysql_native_password by 'djRmqSxxdppt8rHV'"
	docker-compose exec fpm php bin/console doctrine:database:create --if-not-exists
	docker-compose exec fpm php bin/console doctrine:schema:drop --force
	docker-compose exec fpm php bin/console doctrine:schema:create
	docker-compose exec fpm php bin/console doctrine:query:sql "INSERT INTO user (username, password) VALUES ('admin','1izyRXm4TLc=')"

exec:
	docker-compose exec fpm zsh
