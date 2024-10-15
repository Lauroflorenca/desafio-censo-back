# Desafio Laravel/Angular

Este projeto contém a parte backend construída em Laravel e o frontend em Angular. O backend processa o upload de um censo, armazena temporariamente os dados e permite a confirmação para salvar os dados finais.

# Pré-requisitos

Certifique-se de ter as seguintes ferramentas instaladas:

PHP >= 8.1

Composer >= 2.x

Node.js >= 16.x

NPM >= 8.x

MySQL

# Instruções para o Backend (Laravel)
Clone o repositório:

``` 
git clone https://github.com/Lauroflorenca/desafio-censo-back.git
```

```
cd seu-repositorio/backend
```


Instale as dependências do Laravel:


```
composer install
```

# Configuração do banco de dados:

Copie o arquivo de exemplo .env:

```
cp .env.example .env
```

Abra o arquivo .env e configure as variáveis de banco de dados:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=usuario
DB_PASSWORD=senha
```

Gere a chave da aplicação:

```
php artisan key:generate
```


Execute as migrações:

```
php artisan migrate
```


Suba o servidor de desenvolvimento:

```
php artisan serve
```

O backend estará rodando em http://127.0.0.1:8000.



# Instruções para o Frontend (Angular)
Acesse a pasta do projeto:



Instale as dependências do Angular:

```
npm install
```

Rode o servidor de desenvolvimento:

```
ng serve
```

O frontend estará rodando em http://localhost:4200.
