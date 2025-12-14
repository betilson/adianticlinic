<?php
/**
 * Home Page - Página Inicial Pública da Clínica
 * Arquivo standalone fora do Adianti Framework
 *
 * @version    1.0
 * @author     Clinica SAAS
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Builder Clinic - Sistema de Gestão de Clínicas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="shortcut icon" type="image/png" href="favicon.png" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #fafafa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* HEADER */
        .ph-header {
            background: linear-gradient(135deg, #3a3f4b 0%, #2d3139 100%);
            padding: 8px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .ph-header-logo {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ph-logo-icon {
            width: 36px;
            height: 36px;
        }

        .ph-logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1;
        }

        .ph-logo-builder {
            font-size: 8px;
            font-weight: 600;
            color: #f5a623;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .ph-logo-clinic {
            font-size: 16px;
            font-weight: 800;
            color: #fff;
            letter-spacing: 0.5px;
        }

        .ph-header-login {
            color: #8b9099;
            text-decoration: none;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: color 0.2s;
        }

        .ph-header-login:hover {
            color: #fff;
        }

        /* MAIN CONTENT */
        .ph-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 20px 40px;
            min-height: calc(100vh - 52px);
        }

        /* LOGO CENTRAL */
        .ph-logo-container {
            display: flex;
            align-items: flex-start;
            gap: 5px;
            margin-bottom: 60px;
        }

        .ph-main-logo-icon {
            width: 80px;
            height: 95px;
        }

        .ph-main-logo-text {
            display: flex;
            flex-direction: column;
            padding-top: 10px;
        }

        .ph-main-builder {
            font-size: 22px;
            font-weight: 700;
            color: #f5a623;
            letter-spacing: 3px;
            line-height: 1;
        }

        .ph-main-clinic {
            font-size: 52px;
            font-weight: 900;
            color: #4a4f5a;
            letter-spacing: 1px;
            line-height: 0.9;
            text-shadow: 2px 2px 0 #d0d3d9, 3px 3px 0 #c0c3c9;
        }

        /* ACTION CARDS */
        .ph-cards {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 30px;
        }

        .ph-card {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 220px;
            height: 130px;
            background: #fff;
            border: 2px solid #dde0e5;
            border-radius: 8px;
            text-decoration: none;
            color: #5a6069;
            transition: all 0.25s ease;
            flex-direction: column;
        }

        .ph-card i {
            font-size: 28px;
            color: #7a8089;
        }

        .ph-card span {
            font-size: 15px;
            font-weight: 500;
            color: #5a6069;
        }

        .ph-card:hover {
            border-color: #f5a623;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 166, 35, 0.15);
        }

        .ph-card:hover i {
            color: #f5a623;
        }

        .ph-card.highlight {
            background: linear-gradient(180deg, #fef7eb 0%, #fdefd8 100%);
            border-color: #f5a623;
        }

        .ph-card.highlight i {
            color: #f5a623;
        }

        .ph-card.highlight span {
            color: #d4920c;
        }

        /* ADMIN LINK */
        .ph-admin-link {
            color: #7a8089;
            text-decoration: none;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 10px;
            transition: color 0.2s;
        }

        .ph-admin-link:hover {
            color: #4a4f5a;
            text-decoration: underline;
        }

        /* AUTH LINKS */
        .ph-auth-links {
            display: flex;
            gap: 20px;
            margin-top: 15px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .ph-auth-links a {
            color: #5a6069;
            text-decoration: none;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .ph-auth-links a i {
            font-size: 14px;
        }

        .ph-create-account {
            background: linear-gradient(180deg, #f5a623 0%, #e69510 100%);
            color: #fff !important;
        }

        .ph-create-account:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 166, 35, 0.3);
        }

        .ph-forgot-password {
            background: #f0f1f3;
        }

        .ph-forgot-password:hover {
            background: #e5e7ea;
            color: #4a4f5a;
        }

        /* RESPONSIVE */
        @media (max-width: 700px) {
            .ph-cards {
                flex-direction: column;
                align-items: center;
            }

            .ph-card {
                width: 100%;
                max-width: 300px;
                height: 90px;
                flex-direction: row;
            }

            .ph-main-clinic {
                font-size: 36px;
            }

            .ph-main-builder {
                font-size: 16px;
            }

            .ph-main-logo-icon {
                width: 60px;
                height: 70px;
            }

            .ph-auth-links {
                flex-direction: column;
                align-items: center;
            }

            .ph-auth-links a {
                width: 100%;
                max-width: 200px;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <!-- HEADER -->
    <header class="ph-header">
        <div class="ph-header-logo">
            <div class="ph-logo-icon">
                <svg viewBox="0 0 40 40" width="36" height="36">
                    <rect x="15" y="5" width="10" height="30" rx="2" fill="#4a4f5a" />
                    <rect x="5" y="15" width="30" height="10" rx="2" fill="#4a4f5a" />
                    <path d="M20 14 C17 10, 12 10, 12 15 C12 20, 20 26, 20 26 C20 26, 28 20, 28 15 C28 10, 23 10, 20 14"
                        fill="#f5a623" />
                </svg>
            </div>
            <div class="ph-logo-text">
                <span class="ph-logo-builder">BUILDER</span>
                <span class="ph-logo-clinic">CLINIC</span>
            </div>
        </div>
        <a href="login.php" class="ph-header-login">
            <i class="fas fa-sign-in-alt"></i> login admin
        </a>
    </header>

    <!-- MAIN -->
    <main class="ph-main">
        <!-- Logo Central -->
        <div class="ph-logo-container">
            <div class="ph-main-logo-icon">
                <svg viewBox="0 0 80 100" width="80" height="95">
                    <rect x="28" y="5" width="24" height="70" rx="4" fill="#5a6069" />
                    <rect x="5" y="28" width="70" height="24" rx="4" fill="#5a6069" />
                    <rect x="30" y="7" width="20" height="66" rx="3" fill="#4a4f5a" />
                    <rect x="7" y="30" width="66" height="20" rx="3" fill="#4a4f5a" />
                    <path d="M40 28 C34 18, 22 18, 22 32 C22 46, 40 60, 40 60 C40 60, 58 46, 58 32 C58 18, 46 18, 40 28"
                        fill="#f5a623" />
                    <ellipse cx="30" cy="30" rx="4" ry="3" fill="#fcd181" opacity="0.6" />
                </svg>
            </div>
            <div class="ph-main-logo-text">
                <span class="ph-main-builder">BUILDER</span>
                <span class="ph-main-clinic">CLINIC</span>
            </div>
        </div>

        <!-- Action Cards -->
        <div class="ph-cards">
            <a href="index.php?class=PublicAgendamento" class="ph-card">
                <i class="fas fa-calendar-alt"></i>
                <span>Novo agendamento</span>
            </a>
            <a href="index.php?class=PublicDocumentos" class="ph-card">
                <i class="fas fa-file-alt"></i>
                <span>Meus documentos</span>
            </a>
            <a href="index.php?class=PublicValidarDocumento" class="ph-card highlight">
                <i class="fas fa-check-circle"></i>
                <span>Validar documento</span>
            </a>
        </div>

        <!-- Login Administrativo -->
        <a href="login.php" class="ph-admin-link">
            <i class="fas fa-sign-in-alt"></i> login administrativo
        </a>

        <!-- Auth Links -->
        <div class="ph-auth-links">
            <a href="register.php" class="ph-create-account">
                <i class="fas fa-user-plus"></i> criar conta
            </a>
            <a href="forgot_password.php" class="ph-forgot-password">
                <i class="fas fa-key"></i> esqueci a senha
            </a>
        </div>
    </main>
</body>

</html>