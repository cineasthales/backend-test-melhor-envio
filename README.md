# Teste Back-end Melhor Envio

Realizado em Laravel v8.75 por Thales Cezar Castro

## Requisitos
- PHP v7.4
- Composer v.2.2
- MySQL v8
- Phpunit
- Extensões PHP (php-cli, php-zip, php-mysql)

## Como rodar
1. No terminal do Linux, direcione-se para a sua pasta de projetos com o comando `cd`
2. Clone o repositório
```bash
git clone https://github.com/cineasthales/backend-test-melhor-envio.git
```
3. Instale as dependências
```bash
composer install
```
4. Faça uma cópia do arquivo `.env.example`, renomeando-o para `.env`
5. No `.env`, configure as variáveis de ambiente do banco de dados
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=backend_test
DB_USERNAME=root
DB_PASSWORD=password
```
6. Caso o composer não tenha gerado um APP_KEY no .env, rodar
```bash
php artisan key:generate
```
7. Criar um banco de dados MySQL com o mesmo nome configurado em DB_DATABASE
8. Rodar migrations para montar tabelas do banco de dados
```bash
php artisan migrate
```
9. Colocar o arquivo logs.txt em storage/logs
10. Rodar o projeto
```bash
php artisan serve
```

## Rotas
- POST /api/logs -> carrega os dados do arquivo txt e salva no banco de dados, pode ser passado por form-data os campos "offset" e "length" que definem, respectivamente, o deslocamento de linhas a partir do início e a quantidade de linhas a serem processadas no arquivo
- GET /api/reports/requests-by-consumer -> gera o relatório "requisições por consumidor" em csv
- GET /api/reports/requests-by-service -> gera o relatório "requisições por serviço" em csv
- GET /api/reports/average-latencies-by-service -> gera o relatório "tempo médio de request, proxy e gateway" em csv
