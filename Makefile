# Code quality assurance
cqa: phpunit phpmd phpcs

# PHPUnit
phpunit:
	vendor/bin/phpunit

# PHP code sniffer.
phpcs:
	vendor/bin/phpcs --standard=PSR2 --extensions=php public/ src/ tests/

# PHP mess detector.
phpmd:
	vendor/bin/phpmd public/,src/,tests/ text codesize,unusedcode,naming

# Server for development.
server:
	php -S localhost:8080 -t public/

# Runs database migrations for development.
migrations-dev:
	vendor/bin/phinx migrate -e dev-mysql -c src/database.php
