-- =====================================================
-- TRIMAX ENCUESTAS - DATABASE SCHEMA
-- =====================================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS trimax_encuestas 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE trimax_encuestas;

-- =====================================================
-- Tabla: users
-- =====================================================
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','consultor','sede') NOT NULL DEFAULT 'consultor',
  `consultor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `unique_token` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `phone` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_unique_token_unique` (`unique_token`),
  KEY `users_role_index` (`role`),
  KEY `users_is_active_index` (`is_active`),
  KEY `users_consultor_id_index` (`consultor_id`),
  CONSTRAINT `users_consultor_id_foreign` FOREIGN KEY (`consultor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: surveys
-- =====================================================
CREATE TABLE `surveys` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `client_email` varchar(255) DEFAULT NULL,
  `experience_rating` tinyint(4) NOT NULL,
  `service_quality_rating` tinyint(4) DEFAULT NULL,
  `response_time_rating` tinyint(4) DEFAULT NULL,
  `recommendation_rating` tinyint(4) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `surveys_user_id_index` (`user_id`),
  KEY `surveys_created_at_index` (`created_at`),
  KEY `surveys_experience_rating_index` (`experience_rating`),
  CONSTRAINT `surveys_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: personal_access_tokens (Laravel Sanctum)
-- =====================================================
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: password_reset_tokens
-- =====================================================
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: failed_jobs
-- =====================================================
CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: migrations
-- =====================================================
CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DATOS INICIALES
-- =====================================================

-- Admin principal
INSERT INTO `users` (`name`, `email`, `password`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
('Administrador TRIMAX', 'admin@trimax.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, NOW(), NOW());
-- Password: Trimax2024!

-- =====================================================
-- VISTAS ÚTILES
-- =====================================================

-- Vista: Resumen de evaluaciones por usuario
CREATE OR REPLACE VIEW v_user_ratings_summary AS
SELECT 
    u.id,
    u.name,
    u.role,
    u.location,
    u.consultor_id,
    c.name as consultor_name,
    COUNT(s.id) as total_surveys,
    AVG(s.experience_rating) as avg_rating,
    SUM(CASE WHEN s.experience_rating = 4 THEN 1 ELSE 0 END) as excellent_count,
    SUM(CASE WHEN s.experience_rating = 3 THEN 1 ELSE 0 END) as good_count,
    SUM(CASE WHEN s.experience_rating = 2 THEN 1 ELSE 0 END) as regular_count,
    SUM(CASE WHEN s.experience_rating = 1 THEN 1 ELSE 0 END) as bad_count,
    ROUND((SUM(CASE WHEN s.experience_rating = 4 THEN 1 ELSE 0 END) / COUNT(s.id)) * 100, 2) as excellent_percentage
FROM users u
LEFT JOIN users c ON u.consultor_id = c.id
LEFT JOIN surveys s ON u.id = s.user_id
WHERE u.role IN ('consultor', 'sede')
GROUP BY u.id, u.name, u.role, u.location, u.consultor_id, c.name;

-- Vista: Encuestas recientes con detalles
CREATE OR REPLACE VIEW v_recent_surveys AS
SELECT 
    s.id,
    s.created_at,
    u.name as user_name,
    u.role as user_role,
    u.location,
    s.client_name,
    s.experience_rating,
    CASE s.experience_rating
        WHEN 4 THEN 'Excelente'
        WHEN 3 THEN 'Bueno'
        WHEN 2 THEN 'Regular'
        WHEN 1 THEN 'Malo'
    END as rating_text,
    s.comments
FROM surveys s
INNER JOIN users u ON s.user_id = u.id
ORDER BY s.created_at DESC;

-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS
-- =====================================================

DELIMITER $$

-- Procedimiento: Obtener estadísticas por periodo
CREATE PROCEDURE sp_get_period_statistics(
    IN p_start_date DATE,
    IN p_end_date DATE
)
BEGIN
    SELECT 
        COUNT(*) as total_surveys,
        AVG(experience_rating) as avg_rating,
        SUM(CASE WHEN experience_rating = 4 THEN 1 ELSE 0 END) as excellent,
        SUM(CASE WHEN experience_rating = 3 THEN 1 ELSE 0 END) as good,
        SUM(CASE WHEN experience_rating = 2 THEN 1 ELSE 0 END) as regular,
        SUM(CASE WHEN experience_rating = 1 THEN 1 ELSE 0 END) as bad
    FROM surveys
    WHERE DATE(created_at) BETWEEN p_start_date AND p_end_date;
END$$

-- Procedimiento: Top consultores del mes
CREATE PROCEDURE sp_top_consultores_month()
BEGIN
    SELECT 
        u.id,
        u.name,
        COUNT(s.id) as total_surveys,
        AVG(s.experience_rating) as avg_rating,
        SUM(CASE WHEN s.experience_rating = 4 THEN 1 ELSE 0 END) as excellent_count
    FROM users u
    INNER JOIN surveys s ON u.id = s.user_id
    WHERE u.role = 'consultor'
    AND MONTH(s.created_at) = MONTH(CURRENT_DATE())
    AND YEAR(s.created_at) = YEAR(CURRENT_DATE())
    GROUP BY u.id, u.name
    HAVING COUNT(s.id) >= 5
    ORDER BY avg_rating DESC, total_surveys DESC
    LIMIT 10;
END$$

DELIMITER ;

-- =====================================================
-- ÍNDICES ADICIONALES PARA PERFORMANCE
-- =====================================================

-- Índice compuesto para búsquedas por fecha y usuario
CREATE INDEX idx_surveys_user_date ON surveys(user_id, created_at DESC);

-- Índice para búsqueda de usuarios activos
CREATE INDEX idx_users_active_role ON users(is_active, role);

-- =====================================================
-- TRIGGERS
-- =====================================================

DELIMITER $$

-- Trigger: Validar rating al insertar
CREATE TRIGGER trg_validate_rating_before_insert
BEFORE INSERT ON surveys
FOR EACH ROW
BEGIN
    IF NEW.experience_rating NOT BETWEEN 1 AND 4 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Rating debe estar entre 1 y 4';
    END IF;
END$$

-- Trigger: Actualizar updated_at automáticamente
CREATE TRIGGER trg_surveys_update_timestamp
BEFORE UPDATE ON surveys
FOR EACH ROW
BEGIN
    SET NEW.updated_at = NOW();
END$$

DELIMITER ;

-- =====================================================
-- FIN DEL SCHEMA
-- =====================================================
