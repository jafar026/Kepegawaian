-- Membuat database
CREATE DATABASE kepegawaian;
USE kepegawaian;

-- Tabel utama untuk menyimpan data pegawai
CREATE TABLE pegawai (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    jabatan VARCHAR(100) NOT NULL,
    departemen VARCHAR(100) NOT NULL
);

-- Tabel log untuk mencatat aktivitas pada tabel pegawai
CREATE TABLE pegawai_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pegawai_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    action_date DATETIME NOT NULL
);

-- Trigger untuk mencatat aktivitas INSERT
DELIMITER //
CREATE TRIGGER after_pegawai_insert
AFTER INSERT ON pegawai
FOR EACH ROW
BEGIN
    INSERT INTO pegawai_logs (pegawai_id, action, action_date)
    VALUES (NEW.id, 'INSERT', NOW());
END //
DELIMITER ;

-- Trigger untuk mencatat aktivitas UPDATE
DELIMITER //
CREATE TRIGGER after_pegawai_update
AFTER UPDATE ON pegawai
FOR EACH ROW
BEGIN
    INSERT INTO pegawai_logs (pegawai_id, action, action_date)
    VALUES (NEW.id, 'UPDATE', NOW());
END //
DELIMITER ;

-- Trigger untuk mencatat aktivitas DELETE
DELIMITER //
CREATE TRIGGER after_pegawai_delete
AFTER DELETE ON pegawai
FOR EACH ROW
BEGIN
    INSERT INTO pegawai_logs (pegawai_id, action, action_date)
    VALUES (OLD.id, 'DELETE', NOW());
END //
DELIMITER ;

-- Stored procedure untuk menambahkan pegawai
DELIMITER //
CREATE PROCEDURE tambah_pegawai(IN p_nama VARCHAR(100), IN p_jabatan VARCHAR(100), IN p_departemen VARCHAR(100))
BEGIN
    INSERT INTO pegawai (nama, jabatan, departemen)
    VALUES (p_nama, p_jabatan, p_departemen);
END //
DELIMITER ;

-- Stored procedure untuk memperbarui pegawai
DELIMITER //
CREATE PROCEDURE perbarui_pegawai(IN p_id INT, IN p_nama VARCHAR(100), IN p_jabatan VARCHAR(100), IN p_departemen VARCHAR(100))
BEGIN
    UPDATE pegawai
    SET nama = p_nama, jabatan = p_jabatan, departemen = p_departemen
    WHERE id = p_id;
END //
DELIMITER ;

-- Stored procedure untuk menghapus pegawai
DELIMITER //
CREATE PROCEDURE hapus_pegawai(IN p_id INT)
BEGIN
    DELETE FROM pegawai WHERE id = p_id;
END //
DELIMITER ;