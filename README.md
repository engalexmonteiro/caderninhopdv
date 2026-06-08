# PDV Sistema — PHP + MySQL

Sistema de Ponto de Venda com arquitetura MVC + Repository Pattern + Service Layer.

## Requisitos

- PHP 8.1+
- MySQL 5.7+ / MariaDB 10.3+
- Apache com `mod_rewrite` (XAMPP, Laragon, etc.)

## Instalação

1. Copie a pasta para o servidor (ex: `htdocs/pdv`)
2. Edite `config/db.php` com suas credenciais
3. Configure o **Document Root** do servidor para apontar para a pasta `public/`
   - XAMPP: edite `httpd-vhosts.conf` ou use a pasta `public/` diretamente
   - Laragon: o Laragon já usa `public/` por padrão
4. Acesse `http://localhost/setup.php` para criar o banco e o usuário admin
5. **Apague `setup.php`** após a instalação
6. Acesse `http://localhost/login`

### Acesso Rápido (sem configurar Document Root)
Se não quiser configurar o Document Root, acesse via:
`http://localhost/pdv/public/`

## Credenciais Padrão

| Campo  | Valor         |
|--------|---------------|
| E-mail | admin@pdv.com |
| Senha  | admin123      |

## Arquitetura

```
MVC + Repository Pattern + Service Layer
```

### Fluxo de uma requisição

```
HTTP Request
  → public/index.php  (Front Controller + Router)
      → Controller    (recebe request, chama Service)
          → Service   (regras de negócio)
              → Repository  (acesso ao banco via PDO)
                  → Model   (entidade de dados pura)
          ← retorna dados
      → render()      (passa dados para a View)
  → HTTP Response
```

## Estrutura de Arquivos

```
pdv/
├── app/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── PdvController.php
│   │   ├── ProdutoController.php
│   │   ├── UsuarioController.php
│   │   └── VendaController.php
│   ├── Models/
│   │   ├── Produto.php
│   │   ├── Usuario.php
│   │   ├── Venda.php
│   │   └── VendaItem.php
│   ├── Repositories/
│   │   ├── ProdutoRepository.php
│   │   ├── UsuarioRepository.php
│   │   └── VendaRepository.php
│   ├── Services/
│   │   ├── AuthService.php
│   │   ├── ProdutoService.php
│   │   ├── UsuarioService.php
│   │   └── VendaService.php
│   └── helpers.php
├── config/
│   └── db.php
├── public/               ← Document Root
│   ├── index.php         ← Front Controller
│   ├── .htaccess
│   └── assets/
│       └── style.css
├── views/
│   ├── layout/
│   │   ├── header.php
│   │   └── footer.php
│   ├── auth/login.php
│   ├── dashboard/index.php
│   ├── pdv/index.php
│   ├── produtos/{lista,form}.php
│   ├── usuarios/{lista,form}.php
│   └── vendas/lista.php
├── setup.php             ← apagar após instalação
└── db.sql                ← schema de referência
```

## Responsabilidades por Camada

| Camada       | Responsabilidade                                      |
|--------------|-------------------------------------------------------|
| Model        | Estrutura de dados pura (sem lógica de banco)         |
| Repository   | Toda e qualquer consulta/escrita no banco (PDO aqui)  |
| Service      | Regras de negócio (validação, cálculos, transações)   |
| Controller   | Recebe HTTP, chama Service, chama render() ou json()  |
| View         | HTML puro + variáveis extraídas pelo render()         |
