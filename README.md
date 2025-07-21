# 💸 Grana em Ordem - Projeto em Laravel

Este é um projeto de **aprendizado** desenvolvido com **Laravel** e **PHP**, que simula um sistema simples de **controle financeiro pessoal**. O objetivo é colocar em prática os conhecimentos adquiridos sobre Laravel e desenvolver uma aplicação funcional com cadastro e visualização de transações financeiras.

---

## 🚀 Funcionalidades

- **Cadastro de Transações**
  - Permite registrar **receitas** e **despesas** com:
    - Valor
    - Descrição
    - Data

- **Categorização de Transações**
  - Cada transação pode ser associada a uma **categoria** (ex: Alimentação, Transporte, Lazer, Salário, etc.)

- **Visualização das Transações**
  - Lista de todas as transações cadastradas
  - Filtros por:
    - Tipo (Receita ou Despesa)
    - Categoria

- **Totalização**
  - Exibição do **saldo atual**
  - Soma total de receitas
  - Soma total de despesas

- **Autenticação de Usuário** (opcional)
  - Sistema de login e registro
  - Cada usuário visualiza apenas suas próprias transações

---

## 🛠️ Tecnologias e Ferramentas

- PHP
- Laravel
- Composer

---
=======
# 💸 Grana em Ordem - Seu Controlador Financeiro Pessoal

Este é um projeto prático desenvolvido com Laravel e PHP para simular um sistema simples de controle financeiro pessoal. O principal objetivo é aplicar e consolidar conhecimentos em desenvolvimento web com o framework Laravel, criando uma aplicação funcional para gerenciar receitas e despesas de forma intuitiva.

---

## ✨ Funcionalidades Principais

O **Grana em Ordem** oferece as seguintes funcionalidades para ajudar no controle financeiro:

- **Autenticação de Usuário**  
  Sistema completo de login e registro para que cada usuário tenha seu ambiente seguro e visualize apenas suas próprias transações.

- **Cadastro de Transações**  
  Permite registrar facilmente suas finanças, categorizando-as como receitas ou despesas, incluindo:
  - Valor
  - Descrição detalhada
  - Data da transação

- **Gestão de Transações**
  - **Visualização Detalhada**: Lista todas as transações cadastradas.
  - **Edição**: Altere os detalhes de qualquer transação registrada.
  - **Exclusão**: Remova transações indesejadas.

- **Totalização e Resumo**
  - Exibição do saldo atual (Receitas - Despesas).
  - Somas totais de receitas e despesas para um panorama rápido.

- **Categorização de Transações** *(Ainda a ser implementada)*  
  Funcionalidade planejada: Associar cada transação a uma categoria específica (ex: Alimentação, Transporte, Moradia, Lazer, Salário, etc.) para uma análise mais organizada dos gastos.

- **Filtros de Visualização** *(Ainda a ser implementada)*  
  Funcionalidade planejada: Opções para filtrar a lista de transações por tipo (Receita/Despesa) ou por categoria.

---

## 🛠️ Tecnologias e Ferramentas Utilizadas

- **PHP**: Linguagem de programação backend.
- **Laravel**: Framework PHP para desenvolvimento web robusto e rápido.
- **Composer**: Gerenciador de dependências para PHP.
- **SQLite**: Banco de dados leve e baseado em arquivo, ideal para desenvolvimento.
- **Node.js & NPM**: Para gerenciamento de pacotes JavaScript e compilação de assets frontend.
- **Vite**: Compilador de assets frontend utilizado pelo Laravel.
- **Tailwind CSS**: Framework CSS utility-first para estilização rápida e responsiva.
- **Git**: Sistema de controle de versão.
- **WSL 2 (Windows Subsystem for Linux)**: Ambiente de desenvolvimento Linux integrado ao Windows.

---

## 🚀 Como Executar o Projeto

### ✅ Pré-requisitos

Certifique-se de ter o seguinte instalado e configurado no seu ambiente **WSL (Ubuntu)**:

#### PHP (versão 8.2+ recomendada)

```bash
sudo apt update && sudo apt install -y php-cli php-mbstring php-xml php-bcmath php-sqlite3 unzip git curl 
```

### Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```
### Node.js e NPM (versão LTS recomendada):
```bash
sudo apt install -y nodejs npm
```
### Passos de Instalação
Clone o Repositório:
Abra seu terminal Ubuntu (WSL) e clone este repositório para o seu computador:
```bash
git clone https://github.com/BeatrizVCosta/grana-em-ordem.git
```


### Navegue até a Pasta do Projeto:
```bash
cd grana-em-ordem
```
### Instale as Dependências do PHP:
```bash
composer install
```
### Configure o Arquivo .env:
O Laravel usa um arquivo .env para configurações de ambiente. Copie o arquivo de exemplo:
```bash
cp .env.example .env
```

### Gere a Chave da Aplicação:
```bash
php artisan key:generate
```
### Crie o Arquivo do Banco de Dados SQLite:
```bash
touch database/database.sqlite
```
### Execute as Migrações do Banco de Dados:
Isso criará as tabelas necessárias no seu banco de dados, incluindo usuários e transações:
```bash
php artisan migrate
```
### Instale as Dependências do Frontend e Compile os Assets:
Certifique-se de executar esses comandos no seu terminal WSL (Ubuntu) e aguarde a conclusão de cada um.
```bash
npm install
npm run dev
```
### Inicie o Servidor de Desenvolvimento:
```bash
php artisan serve
```
### Acesso à Aplicação
### Após iniciar o servidor, abra seu navegador e acesse:

http://127.0.0.1:8000

Você deverá ver a página inicial do Laravel, com os links para Log in e Register. Registre-se para começar a usar seu controlador de gastos!
