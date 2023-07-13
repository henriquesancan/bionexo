## Bionexo - BeeCare

### Objetivos:

- [X] Extrair e salvar dados de tabela HTML no banco de dados.
- [X] Preencher formulário externo com dados aleatórios.
- [X] Baixar arquivo a partir de link externo e renomeá-lo.
- [X] Fazer upload do arquivo recém-baixado em link externo.
- [X] Extrair e salvar dados de PDF em planilha Excel ou CSV.

---
### Clonar o projeto:
Para clonar o projeto, abra o terminal e execute o seguinte comando:

```bash
git clone https://github.com/henriquesancan/bionexo.git
```

Isso criará uma cópia local do repositório em sua máquina.

### Instalar as dependências:
Após clonar o projeto, navegue até o diretório raiz do projeto e execute o seguinte comando para instalar as dependências usando o Composer:

```bash
composer install
```

Certifique-se de que o Composer esteja instalado em seu sistema antes de executar o comando acima.

### Executar o Docker:
Certifique-se de ter o Docker instalado em sua máquina antes de prosseguir.

Com o Docker aberto, navegue até o diretório raiz do projeto e execute o seguinte comando para construir as imagens do Docker:

```bash
docker-compose build
```

Esse comando irá construir as imagens do Docker com base nas configurações definidas no arquivo **docker-compose.yml**.

Após a conclusão da construção das imagens, execute o seguinte comando para iniciar os containers em segundo plano:

```bash
docker-compose up -d
```

Isso iniciará os containers necessários para o projeto Bionexo.

Agora você pode acessar o projeto Bionexo em seu navegador usando o endereço fornecido nas configurações do Docker.

**Observação:** Certifique-se de que as portas necessárias estejam disponíveis em sua máquina e que não estejam sendo usadas por outros serviços.

---
### Tecnologias utilizadas:

- Docker 24.0
- Laravel 10.14
- MySQL 8.0
- PHP 8.1
- Selenium 4.10
