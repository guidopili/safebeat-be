.PHONY: up start exec

up:
	docker-compose up -d --force-recreate

start: up
	sleep 5
	docker-compose exec -T mysql mysql -uroot -pdjRmqSxxdppt8rHV <<< "alter user 'root'@'%'identified with mysql_native_password by 'djRmqSxxdppt8rHV'"
	docker-compose exec fpm php bin/console doctrine:database:create --if-not-exists
	docker-compose exec fpm php bin/console doctrine:schema:drop --force
	docker-compose exec fpm php bin/console doctrine:schema:create

exec:
	docker-compose exec fpm zsh

build:
	docker build -t guidopili/safebeat-be-php:prod -f Dockerfile.prod .
