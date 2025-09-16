## Documentation - Credenciais (Nova Configuração)

# Moodle Admin:

Usuário: admin
Senha: Admin123! | #Teste17


# Banco de Dados:

Host: mariadb
Banco: moodle
Usuário: moodle_user
Senha: moodle_pass123


# phpMyAdmin:

Usuário: root
Senha: rootpassword123

## Comandos Mais Usados

| Ação        | Comando                | Preserva       |
| :---------  | :---:                  | --------:      |
| Pausar      | docker-compose pause   | Tudo na RAM    |
| Despausar   | docker-compose unpause | Tudo           |
| Parar       | docker-compose stop    | Todos os dados |
| Iniciar     | docker-compose start   | Todos os dados |
| Reiniciar   | docker-compose restart | Todos os dados | → docker-compose restart moodle

## O que NÃO Fazer (Se Quiser Manter Tudo)

# NÃO use estes comandos:
docker-compose down        # Remove containers (mas mantém volumes)
docker-compose down -v     # Remove containers E volumes (PERDE DADOS!)
docker system prune -a     # Remove tudo não usado


## Comandos Úteis para Verificar Status

# Ver status dos containers
docker-compose ps

# Ver containers em execução
docker ps

# Ver logs em tempo real
docker-compose logs -f

# Ver logs de um serviço específico
docker-compose logs -f moodle

## Reiniciar (stop + start em um comando)

# Reiniciar todos os serviços
docker-compose restart

# Reiniciar serviço específico (ex: apenas o Moodle)
docker-compose restart moodle

# Para configuração específica
docker-compose -f docker-compose-otimizado.yml restart

# Acessar os serviços
Moodle: http://localhost:8080
phpMyAdmin: http://localhost:8081

# Comando para executar a limpeza no docker:
docker exec -it moodle_app php admin/cli/upgrade.php
docker exec -it moodle_app php admin/cli/purge_caches.php
