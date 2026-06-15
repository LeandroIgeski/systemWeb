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

USE sistema_noticias;

CREATE TABLE noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    data_noticia DATE NOT NULL,
    texto TEXT NOT NULL,
    imagem VARCHAR(255),
    usuario_id INT NOT NULL,
    ativo TINYINT(1) DEFAULT 1,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

ALTER TABLE noticias
ADD COLUMN desativado_por ENUM('autor', 'admin') DEFAULT NULL;