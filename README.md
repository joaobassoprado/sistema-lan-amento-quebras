# Sistema de Lançamento de Quebras – Laravel 10

Este sistema foi desenvolvido para **registrar e gerenciar quebras de garrafas e produtos** da empresa, garantindo rastreabilidade, controle e organização dos lançamentos realizados pelos colaboradores.

O projeto utiliza **Laravel 10.48.28**, **Docker**, **Vite**, **TailwindCSS** e autenticação via **Active Directory (AD)**, com controle hierárquico de permissões.

---

## 🚀 Tecnologias Utilizadas

- **Laravel 10.48.28**
- **PHP 8+**
- **Docker & Docker Compose**
- **MySQL / MariaDB**
- **TailwindCSS**
- **Vite**
- **NPM / NodeJS**
- **Autenticação via Active Directory (AD)**
- **Hierarquia de acessos (Admin / Usuário Comum)**

---

## 🔐 Autenticação e Permissões

O sistema utiliza:

- Login integrado com **AD (Active Directory)**
- Controle de permissões nativo:
    - **Administrador** → possui acesso total ao sistema
    - **Usuário Padrão** → permite lançar quebras e consultar relatórios liberados

---

## 🧩 Funcionalidades do Sistema

### ✔ Registro de Quebras

- Cadastro de quebras de produtos e garrafas
- Vínculo de produto, funcionário e quantidade
- Setor, área, motivo, turno e observações
- Registro automático da data do lançamento

### ✔ Gerenciamento de Usuários

- Login com AD
- Definição de nível de permissão (admin / padrão)

### ✔ Relatórios

- Resumo de quebras por período
- Filtro por setor, produto e funcionário
- Consulta rápida e responsiva

---

## 🐳 Rodando o Projeto com Docker

Certifique-se de ter instalados:

- **Docker**
- **Docker Compose**

### 1. Clonar o repositório

```bash
git clone <url-do-repositorio>
cd <nome-do-projeto>
```
