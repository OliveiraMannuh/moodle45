#!/bin/bash

# Script de configuração do Moodle 4.5 com Docker
echo "🚀 Configurando Moodle 4.5 com Docker..."

# Criar estrutura de diretórios
echo "📁 Criando estrutura de diretórios..."
mkdir -p mysql-init
mkdir -p moodle

# Baixar Moodle 4.5
echo "⬇️ Baixando Moodle 4.5..."
if [ ! -f "moodle-4.5.tgz" ]; then
    wget https://download.moodle.org/download.php/direct/stable405/moodle-4.5.tgz
fi

# Extrair Moodle
echo "📦 Extraindo Moodle..."
if [ ! -d "moodle/index.php" ]; then
    tar -xzf moodle-4.5.tgz --strip-components=1 -C moodle/
fi

# Criar arquivo de inicialização do MySQL
echo "🗄️ Criando script de inicialização do MySQL..."
cat > mysql-init/01-init.sql << 'EOF'
-- Configuração inicial do banco Moodle com charset correto
ALTER DATABASE moodle CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Garantir que as tabelas usem o charset correto
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
EOF

# Configurar permissões
echo "🔐 Configurando permissões..."
chmod -R 755 moodle/
chmod +x setup.sh

# Iniciar containers
echo "🐳 Iniciando containers Docker..."
docker-compose up -d

echo ""
echo "✅ Configuração concluída!"
echo ""
echo "🌐 Acesse o Moodle em: http://localhost:8080"
echo "🗄️ Acesse o phpMyAdmin em: http://localhost:8081"
echo ""
echo "📋 Credenciais do banco:"
echo "   Host: mariadb"
echo "   Banco: moodle"
echo "   Usuário: moodle_user"
echo "   Senha: moodle_pass123"
echo ""
echo "📋 Credenciais phpMyAdmin:"
echo "   Usuário: root"
echo "   Senha: rootpassword123"
echo ""
echo "⏳ Aguarde alguns minutos para os containers iniciarem completamente..."
echo "📖 Siga o assistente de instalação do Moodle no navegador."