<p align="center">
  <img src="public/img/logo-bragante.png" width="120" alt="Bragante Logo">
</p>

<h1 align="center">Show de Bola</h1>

<p align="center">
  Sistema de gerenciamento de reservas do Complexo Esportivo Bragante.<br>
  Controle de quadras esportivas e churrasqueiras com grade semanal interativa.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white" alt="PHP 8.3">
  <img src="https://img.shields.io/badge/Laravel-11-FF2D20?logo=laravel&logoColor=white" alt="Laravel 11">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white" alt="MySQL 8.0">
  <img src="https://img.shields.io/badge/Redis-7-DC382D?logo=redis&logoColor=white" alt="Redis 7">
</p>

---

## Sobre

O **Show de Bola** é o sistema interno de reservas do Complexo Esportivo Bragante. Permite que operadores gerenciem a locação de quadras e churrasqueiras através de uma grade semanal onde é possível criar, editar e excluir reservas clicando nas células de horário.

### Recursos

- **Dashboard** com estatísticas em tempo real (reservas ativas, fixas, do dia, por recurso)
- **Grade semanal** interativa por recurso com criação de reservas por clique
- **Reservas fixas e únicas** com controle de recorrência
- **Histórico** completo de alterações
- **Gestão de usuários** com roles e permissões granulares
- **Tema escuro/claro** com persistência
- **Layout responsivo** — desktop (topnav) e mobile (bottomnav + bottom sheet)
- **Auditoria** de todas as ações via Owen Audits

## Stack

| Camada | Tecnologia |
|--------|-----------|
| Backend | PHP 8.3, Laravel 11, Eloquent ORM |
| Frontend | Blade, CSS puro (design system próprio), JavaScript vanilla |
| Banco de dados | MySQL 8.0 |
| Cache/Session | Redis 7 |
| Autenticação | Laravel Breeze |
| Permissões | Spatie Permission |
| Auditoria | Owen-IT Auditing |
| Ícones | Flaticon Uicons |
| Fontes | Inter (Google Fonts) |

## Requisitos

- PHP >= 8.3
- Composer >= 2.x
- MySQL >= 8.0
- Redis >= 7.x
- Node.js >= 18.x (apenas se precisar compilar assets)

## Instalação

```bash
# 1. Clonar o repositório
git clone https://github.com/Wesley-Prestes-Pereira/AppBragante-1.git
cd AppBragante-1

# 2. Instalar dependências PHP
composer install

# 3. Configurar ambiente
cp .env.example .env
php artisan key:generate

# 4. Configurar o .env com seus dados
#    DB_DATABASE, DB_USERNAME, DB_PASSWORD
#    REDIS_HOST, REDIS_PORT

# 5. Executar migrations e seeders
php artisan migrate --seed

# 6. Criar link simbólico do storage
php artisan storage:link

# 7. Iniciar o servidor
php artisan serve
```

## Variáveis de Ambiente

| Variável | Descrição |
|----------|-----------|
| `APP_URL` | URL da aplicação |
| `DB_HOST` | Host do MySQL |
| `DB_DATABASE` | Nome do banco |
| `DB_USERNAME` | Usuário do banco |
| `DB_PASSWORD` | Senha do banco |
| `REDIS_HOST` | Host do Redis |
| `CACHE_STORE` | `redis` |
| `SESSION_DRIVER` | `redis` |

## Estrutura do Projeto

```
app/
├── Http/Controllers/
│   ├── DashboardController.php
│   ├── ReservaController.php
│   └── UsuarioController.php
├── Models/
│   ├── Recurso.php
│   ├── Reserva.php
│   └── User.php
resources/views/
├── layouts/
│   ├── app.blade.php          # Layout principal (autenticado)
│   └── guest.blade.php        # Layout de login
├── auth/
│   └── login.blade.php
├── dashboard/
│   └── index.blade.php
├── reservas/
│   ├── show.blade.php         # Grade semanal
│   └── historico.blade.php
├── usuarios/
│   └── index.blade.php
└── errors/
    ├── 404.blade.php
    ├── 403.blade.php
    ├── 419.blade.php
    ├── 429.blade.php
    ├── 500.blade.php
    └── 503.blade.php
```

## Migração

Este sistema é uma reescrita completa da versão anterior que utilizava Next.js + Prisma + JWT. A migração para Laravel 11 incluiu:

- Reestruturação do banco de dados com correção de categorização de recursos
- Limpeza inteligente de dados (extração de telefones de nomes de clientes, reclassificação de tipos de reserva)
- Eliminação total de Bootstrap, SweetAlert2 e Bootstrap Icons
- Design system próprio com CSS variables, tokens de tema e componentes customizados
- Sistema de permissões granular (a versão anterior tinha auth hardcoded)

## Autor

Desenvolvido por **Wesley Prestes Pereira**.

## Licença

Uso interno — Complexo Esportivo Bragante.