-- Configuração inicial do banco Moodle com charset correto
ALTER DATABASE moodle CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Garantir que as tabelas usem o charset correto
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
