# Desafio Backend

## Descrição

API para gerenciar lugares.

### Executando o projeto.

Para executar o projeto, primeiro você precisa clonar o repositório em sua máquina. Em seguida, navegue até o diretório do projeto e crie o .env a partir do exemplo

```bash
git clone https://github.com/erminiogabriel/laravel-challenge
cd laravel-challenge
mv .env.example .env
```

Para instalar as dependências do aplicativo execute o seguinte comando. Este comando utiliza um pequeno container Docker contendo o PHP e o Composer para instalar as dependências do aplicativo.

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```

Inicie os containers Docker usando o Sail com o seguinte comando:

```bash
./vendor/bin/sail up -d
```

Execute as migrações do banco de dados usando o seguinte comando:

```bash
./vendor/bin/sail artisan migrate
```

O projeto estará rodando em [http://localhost](http://localhost)

### Insomnia

Se você estiver usando o Insomnia para testar a API, você pode importar o arquivo Insomnia.json fornecido neste repositório.

### Rotas

---

-`GET /api/places`: método para listar os locais cadastrados

A solicitação pode incluir os seguintes query params:

-   `page` (opcional): Número da página para paginar os resultados.
-   `name` (opcional): Filtra os locais pelo nome.

Exemplo de solicitação: -`GET /api/places?page=1&name=exemplo`

---

-`GET /api/places/:id`: método para visualizar um local específico

---

-`POST /api/places`: método para criar um novo local

Corpo da requisição:

```bash
{
	"name": "Nome",
	"city": "Lages",
	"state": "Santa Catarina"
}
```

---

-`PUT /api/places/:id`: método para atualizar um local existente

Corpo da requisição:

```bash
{
	"name": "Nome",
	"city": "Brusque",
	"state": "Santa Catarina"
}
```

---

-`DELETE /api/places/:id`: método para excluir um local específico

### Testes

Para executar os testes automatizados, execute o comando:

```bash
./vendor/bin/sail test
```
