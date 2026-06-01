CREATE DATABASE sistema_noticias;
USE sistema_noticias;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('admin','comum') NOT NULL,
    ativo BOOLEAN DEFAULT TRUE
);

INSERT INTO usuarios (nome,email,senha,tipo,ativo)
VALUES
(
'Administrador',
'admin@exemplo.com',
'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
'admin',
1
);

INSERT INTO usuarios (nome,email,senha,tipo,ativo)
VALUES
(
'Usuário Comum',
'user@exemplo.com',
'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
'comum',
1
);